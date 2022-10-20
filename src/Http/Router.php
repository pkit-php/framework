<?php

namespace Pkit\Http;

use Pkit\Http\Router\RouterEnv;
use Pkit\Http\Router\Debug;
use Pkit\Http\Router\Routes;
use Pkit\Throwable\Error;
use Phutilities\Sanitize;
use Phutilities\Env;
use Phutilities\FS;
use Phutilities\Text;
use Throwable;

class Router extends RouterEnv
{
  private static string
    $uri,
    $file;

  private static array $params = [];
  private static ?string
    $especialRoute = null;

  public static function run()
  {
    self::setUri($_SERVER["REQUEST_URI"]);
    self::setFileAndParams();
    $request = new Request;
    if (strlen(self::$file)) {
      $extension = FS::getExtension(self::$file);
      if ($extension != "php") {
        exit(self::getResponseForMimeFile(self::$file));
      } else {
        $err = Error::tryRun(function () use ($request) {
          exit(self::runRoute(self::$file, $request));
        });
      }
    } else {
      $err = new Error(
        "page '" . self::$uri . "' not found",
        Status::NOT_FOUND
      );
    }

    if (@file(self::$especialRoute))
      $err = Error::tryRun(function () use ($request, $err) {
        exit(self::runRoute(self::$especialRoute, $request, $err));
      });

    if (Env::getEnvOrValue("PKIT_DEBUG", "false") == "true")
      exit(Debug::error($request, $err));
    else
      exit(new Response("", $err->getCode()));
  }

  public static function getUri()
  {
    return self::$uri;
  }

  public static function getParams()
  {
    return self::$params;
  }

  public static function getFile()
  {
    return self::$file;
  }

  private static function setUri($requestUri)
  {
    $uri = Sanitize::uri($requestUri);
    if (self::getSubDomain()) {
      $host = $_SERVER['HTTP_HOST'];
      $subdomain = explode($host, ".")[0];
      if (
        $subdomain != "www" &&
        !is_numeric($subdomain) &&
        preg_match("/^\p{L}+$/u", $subdomain)
      )
        $uri = "/" . $subdomain . rtrim($uri, "/");
    }
    self::$uri = $uri;
  }

  private static function setFileAndParams()
  {
    $filePublic = self::getPublicPath() . str_replace("/../", "/", self::$uri);
    if (@file($filePublic)) {
      self::$file = $filePublic;
      return;
    }
    $params = [];
    self::$file = FS::someFile(self::getRoutePath(), function ($file) use (&$params) {
      $route = Text::removeFromStart($file, self::$routePath);
      if (str_ends_with($route, "/*.php"))
        return false;

      $route = str_ends_with($route, "/index.php")
        ? Text::removeFromEnd($route, "index.php")
        : Text::removeFromEnd($route, ".php");

      return Routes::matchRouteAndParams($route, self::$uri, $params);
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
      throw new Error("The route $route was not a valid return", 500);

    if ($return === 1 || is_null($return))
      return "";

    return $return($request, $err);
  }

  private static function getResponseForMimeFile($file): Response
  {
    $content = file_get_contents($file);
    $extension = FS::getExtension($file);

    if (($mime_content = ContentType::getContentType($extension))
      || ($mime_content = mime_content_type(self::$file))
    )
      return (new Response($content))
        ->header("Content-Type", $mime_content);
    else
      return new Response("", Status::UNSUPPORTED_MEDIA_TYPE);
  }
}
