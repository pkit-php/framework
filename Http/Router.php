<?php

namespace Pkit\Http;

use Pkit\Private\Debug;
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
    $especialRoute,
    $message = null;

  public static function init(string $routePath)
  {
    $routes = Map::mapPhpFiles($routePath, '/');
    $routes = Map::mapPhpFiles($routePath, '/');
    self::$uri = Sanitize::sanitizeURI($_SERVER['REQUEST_URI']);
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
