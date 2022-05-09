<?php

namespace Pkit\Http;

use Pkit\Private\Debug;
use Pkit\Private\Env;
use Pkit\Private\Map;
use Pkit\Private\Routes;
use Pkit\Utils\Sanitize;

class Router
{
  private static string
    $uri,
    $file;

  private static Request $request;
  private static Response $response;

  private static array $params = [];
  private static ?string
    $especialRoute = null,
    $message = null,
    $routePath = null,
    $publicPath = null;

  public static function config(string $routePath, string $publicPath = null)
  {
    self::$routePath = rtrim($routePath, "/");
    self::$publicPath = rtrim($publicPath, "/");
  }

  public static function getRoutePath()
  {
    if (is_null(self::$routePath)) {
      self::$routePath = Env::getEnvOrValue("ROUTES_PATH", $_SERVER["DOCUMENT_ROOT"] . "/routes");
    }
    return self::$routePath;
  }

  public static function getPublicPath()
  {
    if (is_null(self::$publicPath)) {
      self::$publicPath = Env::getEnvOrValue("PUBLIC_PATH", $_SERVER["DOCUMENT_ROOT"] . "/public");
    }
    return self::$publicPath;
  }

  private static function init()
  {
    self::$uri = Sanitize::sanitizeURI($_SERVER['REQUEST_URI']);
    $filePublic = file(self::getPublicPath() . self::$uri);
    if (file(self::getPublicPath() . self::$uri)) {
      self::$file = $filePublic;
    } else {
      self::setFileAndParams();
    }
  }

  private static function setFileAndParams()
  {
    $routes = Map::mapPhpFiles(self::getRoutePath(), '/');
    self::$especialRoute = $routes['/*'];
    unset($routes['/*']);
    $match = Routes::mathRoutes($routes, self::$uri);
    [self::$file, self::$params] = [$match[0], $match[1]];
  }

  private static function includeFile()
  {
    include self::$file;

    $extension = '.' . @end(explode('.', self::$file));
    if ($extension != '.php') {
      (new Response)
        ->contentType(mime_content_type($extension) ?? "")
        ->send();
    }
  }

  public static function run()
  {
    self::init();
    self::$request = new Request;
    self::$response = new Response;
    if (strlen(self::$file)) {
      try {
        ob_start();
        self::includeFile();
        exit;
      } catch (\Throwable $th) {
        if (!getenv('PKIT_CLEAR') || getenv('PKIT_CLEAR') == "true") {
          ob_end_clean();
        }
        $code = $th->getCode();
        self::$response->status(
          is_int($code) && $code > 200 && $code != 600
            ? $code
            : 500
        );
        self::$message = $th->getMessage();
      }
    } else {
      self::$response
        ->onlyCode()
        ->status(Status::NOT_FOUND);
      $uri = self::$uri;
      self::$message = "page '$uri' not found";
    }
    self::runEspecialRoute();
  }

  public static function runEspecialRoute()
  {
    if (self::$especialRoute) {
      include self::$especialRoute;
    } else {
      if (getenv('PKIT_DEBUG') == 'true') {
        Debug::log(self::$request, self::$response, self::$message);
      } else {
        self::$response->send();
      }
    }
  }

  public static function getRequestAndResponse()
  {
    return [self::$request, self::$response];
  }

  public static function setMessage($message)
  {
    self::$message = $message;
  }

  public static function getMessage()
  {
    return self::$message;
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
}
