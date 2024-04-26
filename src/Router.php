<?php

namespace Pkit;

use Pkit\Exceptions\Http\Status\NotFound;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Status;
use Pkit\Router\RouterEnv;
use Pkit\Router\Debug;
use Pkit\Router\Route;
use Pkit\Throwable\Error;
use Throwable;

class Router extends RouterEnv
{
  private static string $file;
  private static ?Route
  $especialRoute = null,
  $route = null;

  public static function run()
  {
    $request = Request::getInstance();
    try {
      if ($route = self::getRoute($request->uri)) {
        self::$route = $route;
        $err = Error::tryRun(function () use ($request) {
          exit(self::$route->run($request));
        });
      } else {
        $err = new NotFound(
          "page '" . $request->uri . "' not found",
        );
      }
    } catch (Error $e) {
      $err = $e;
    } catch (\Throwable $th) {
      if (!Status::validate((int) $th->getCode())) {
        $reflection = new \ReflectionObject($th);
        $codeProperty = $reflection->getProperty("code");

        $codeProperty->setAccessible(true);
        $codeProperty->setValue($th, 500);
      }
      $err = $th;
    }

    if (getenv("PKIT_DEBUG") == "true")
      exit(Debug::error($request, $err));
    else
      exit(Response::code($err->getCode()));
  }

  public static function getParams()
  {
    return self::$route ? self::$route->variables : null;
  }

  static private function someFile(string $path, \Closure $map): string|false
  {
    $directory = scandir($path, SCANDIR_SORT_DESCENDING);
    foreach ($directory as $file) {
      if ($file == '.' || $file == '..')
        continue;
      $file = "$path/$file";
      if (@is_dir($file)) {
        if ($result = self::someFile($file, $map))
          return $result;
        continue;
      }

      if ($map($file) == true)
        return $file;

    }

    return false;
  }
  private static function getRoute(string $uri): Route|false
  {
    $filePublic = self::getPublicPath() . str_replace("/../", "/", $uri);
    if (@file($filePublic)) {
      return new Route("$uri", []);
    }

    if (self::someFile(self::getRoutePath(), function ($file) use ($uri, &$route) {
      $routeFile = substr($file, strlen(self::getRoutePath()));
      $route = Route::matchRoute($routeFile, $uri);
      return $route;
    })) {
      return $route;
    } else {
      return false;
    }
  }

}