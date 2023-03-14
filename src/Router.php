<?php

namespace Pkit;

use Pkit\Exceptions\Http\Status\InternalServerError;
use Pkit\Exceptions\Http\Status\NotFound;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Router\RouterEnv;
use Pkit\Router\Debug;
use Pkit\Router\Routes;
use Pkit\Throwable\Error;
use Phutilities\Env;
use Phutilities\FS;
use Phutilities\Text;
use Throwable;

class Router extends RouterEnv
{
  private static string $file;

  private static array $params = [];
  private static ?string
  $especialRoute = null;

  public static function run()
  {
    $request = Request::getInstance();
    self::setFileAndParams($request->uri);
    if (strlen(self::$file)) {
      $extension = FS::getExtension(self::$file);
      if ($extension != "php") {
        exit(Response::mimeFile(self::$file));
      }
      else {
        $err = Error::tryRun(function () use ($request) {
          exit(self::runRoute(self::$file, $request));
        });
      }
    }
    else {
      $err = new NotFound(
        "page '" . $request->uri . "' not found",
      );
    }

    if (@file(self::$especialRoute))
      $err = Error::tryRun(function () use ($request, $err) {
        exit(self::runRoute(self::$especialRoute, $request, $err));
      });

    if (Env::getEnvOrValue("PKIT_DEBUG", "false") == "true")
      exit(Debug::error($request, $err));
    else
      exit(Response::code($err->getCode()));
  }

  public static function getParams()
  {
    return self::$params;
  }

  public static function getFile()
  {
    return self::$file;
  }

  private static function setFileAndParams(string $uri)
  {
    $filePublic = self::getPublicPath() . str_replace("/../", "/", $uri);
    if (@file($filePublic)) {
      self::$file = $filePublic;
      return;
    }
    $params = [];
    self::$file = FS::someFile(self::getRoutePath(), function ($file) use (&$params, $uri) {
      $route = Text::removeFromStart($file, self::$routePath);
      if (str_ends_with($route, "/*.php"))
        return false;

      $route = str_ends_with($route, "/index.php")
        ? Text::removeFromEnd($route, "index.php")
        : Text::removeFromEnd($route, ".php");

      return Routes::matchRouteAndParams($route, $uri, $params);
    }, true) ?? "";
    self::$params = $params;
    self::$especialRoute = self::$routePath . "/*.php";
  }

  private static function runRoute(string $route, Request $request, ?Throwable $err = null): string
  {
    $return = include $route;
    if (is_string($return))
      return $return;

    if (is_callable($return) == false && Env::getEnvOrValue("PKIT_RETURN", "true") == "true")
      throw new InternalServerError("The route $route was not a valid return");

    if ($return === 1 || is_null($return))
      return "";

    return $return($request, $err);
  }

}