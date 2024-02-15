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
      $route = self::getRoute($request->uri);
    } catch (Error $e) {
      $err = $e;
    } catch (\Throwable $th) {
      $reflection = new \ReflectionObject($th);
      $codeProperty = $reflection->getProperty("code");
      $codeProperty->setAccessible(true);
      $codeProperty->setValue($th, Status::validate((int) $th->getCode()) ? $th->getCode() : 500);
      $err = $th;
    }
    if (@$route) {
      self::$route = $route;
      $err = Error::tryRun(function () use ($request) {
        exit(self::$route->run($request));
      });
    } else if (is_null(@$err)) {
      $err = new NotFound(
        "page '" . $request->uri . "' not found",
      );
    }

    if (@file(self::$routePath . "*.php"))
      $err = Error::tryRun(function () use ($request, $err) {
        exit((new Route("*.php", []))->run($request, $err));
      });

    if (getenv("PKIT_DEBUG") == "true")
      exit(Debug::error($request, $err));
    else
      exit(Response::code($err->getCode()));
  }

  public static function getParams()
  {
    return self::$route ? self::$route->variables : null;
  }

  static private function someFile(string $path, \Closure $map, bool $recursive = false): string|false
  {
    $path = rtrim($path);
    $directory = dir($path);
    while ($file = $directory->read()) {
      if ($file == '.' || $file == '..')
        continue;
      $file = "$path/$file";
      if (@dir($file)) {
        if (!$recursive)
          continue;
        if ($result = self::someFile($file, $map, true))
          return $result;
        continue;
      }

      if ($map($file) == true)
        return $file;

    }
    $directory->close();
    return false;
  }
  private static function getRoute(string $uri)
  {
    $filePublic = self::getPublicPath() . str_replace("/../", "/", $uri);
    if (@file($filePublic)) {
      return new Route("$uri", []);
    }

    $route = null;
    self::someFile(self::getRoutePath(), function ($file) use ($uri, &$route) {
      $routeFile = substr($file, strlen(self::$routePath));
      if (str_ends_with($routeFile, "/*.php"))
        return false;

      $route = Route::matchRoute($routeFile, $uri);
      return $route;
    }, true);
    return $route;
  }

}