<?php

namespace Pkit\Http;

use Pkit\Utils\Map;
use Pkit\Utils\Routes;
use Pkit\Utils\Sanitize;
use \Throwable;

class Router
{
  private static string
    $uri,
    $file,
    $especialRoute;

  private static Request $request;
  private static Response $response;

  private static array $params = [];
  private static Throwable $error;

  public static function init(string $routePath)
  {
    $routes = Map::mapPhpFiles($routePath);
    self::$uri = Sanitize::sanitizeURI($_SERVER['REQUEST_URI']);
    self::$especialRoute = $routes['/*/'] ?? "";
    unset($routes['/*/']);
    [self::$file, self::$params] = Routes::mathRoute($routes, self::$uri);
  }

  private static function includeFile()
  {
    include self::$file;

    $extension = '.' . @end(explode('.', self::$file));
    if ($extension != '.php') {
      (new Response)
        ->setContentType(mime_content_type($extension) ?? "")
        ->send();
    }
  }

  public static function run()
  {
    self::$request = new Request;
    self::$response = new Response;
    if (self::$file) {
      try {
        ob_start();
        self::includeFile();
      } catch (\Throwable $th) {
        ob_end_clean();
        self::$response->setHttpCode($th->getCode());
        self::$error = $th;
        self::runEspecialRoute();
      }
    } else {
      self::$response
        ->onlyCode()
        ->notFound();
      self::runEspecialRoute();
    }
  }

  public static function runEspecialRoute()
  {
    if (strlen(self::$especialRoute)) {
      $especialRoute = self::$especialRoute;
      include $especialRoute;
    } else {
      self::$response->send();
    }
  }

  public static function getRequestAndResponse()
  {
    return [self::$request, self::$response];
  }

  public static function getError(): ?\Throwable
  {
    return self::$error;
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
