<?php

namespace Pkit;

use Pkit\Exceptions\Http\Status\InternalServerError;
use Pkit\Exceptions\Http\Status\NotFound;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Router\RouterEnv;
use Pkit\Router\Debug;
use Pkit\Router\Route;
use Pkit\Throwable\Error;
use Phutilities\Env;
use Phutilities\FS;
use Phutilities\Text;
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
    }
    catch (\Throwable $th) {
      $err = $th;
    }
    if (@$route) {
      self::$route = $route;
      $err = Error::tryRun(function () use ($request) {
        exit(self::$route->run($request));
      });
    }
    else if (is_null(@$err)) {
      $err = new NotFound(
        "page '" . $request->uri . "' not found",
      );
    }

    if (@file(self::$routePath . "*.php"))
      $err = Error::tryRun(function () use ($request, $err) {
        exit((new Route("*.php", []))->run($request, $err));
      });

    if (Env::getEnvOrValue("PKIT_DEBUG", "false") == "true")
      exit(Debug::error($request, $err));
    else
      exit(Response::code($err->getCode()));
  }

  public static function getParams()
  {
    return self::$route ? self::$route->variables : null;
  }
  private static function getRoute(string $uri)
  {
    $filePublic = self::getPublicPath() . str_replace("/../", "/", $uri);
    if (@file($filePublic)) {
      return new Route("$uri", []);
    }

    $route = null;
    FS::someFile(self::getRoutePath(), function ($file) use ($uri, &$route) {
      $routeFile = Text::removeFromStart($file, self::$routePath);
      if (str_ends_with($routeFile, "/*.php"))
        return false;

      $route = Route::matchRoute($routeFile, $uri);
      return $route;
    }, true);
    return $route;
  }

}