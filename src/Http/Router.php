<?php

namespace Pkit\Http;

use Pkit\Private\Debug;
use Pkit\Private\Map;
use Pkit\Private\Routes;
use Pkit\Utils\Sanitize;
use Pkit\Utils\Env;
use Pkit\Utils\Text;

class Router
{
  private static string
    $uri,
    $file;

  private static Request $request;
  private static Response $response;

  private static array $params = [];
  private static ?bool $subDomain = null;
  private static ?string
    $especialRoute = null,
    $message = null,
    $routePath = null,
    $publicPath = null,
    $domain = null;

  public static function config(string $routePath, ?string $publicPath = null, ?string $domain = null, bool $subDomain = false)
  {
    self::$routePath = rtrim($routePath, "/");
    self::$publicPath = $publicPath
      ? rtrim($publicPath, "/")
      : null;
    self::$domain = $domain;
    self::$subDomain = $subDomain;
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

  public static function getDomain()
  {
    if (is_null(self::$domain)) {
      self::$domain = Env::getEnvOrValue("DOMAIN", "");
    }
    return self::$domain;
  }

  public static function getSubDomain()
  {
    if (is_null(self::$subDomain)) {
      self::$subDomain = Env::getEnvOrValue("SUB_DOMAIN", null) == "true";
    }
    return self::$subDomain;
  }


  private static function init()
  {
    $uri = Sanitize::sanitizeURI($_SERVER['REQUEST_URI']);
    if (self::getSubDomain()) {
      $host = self::$request->headers['Host'];
      $domain = self::getDomain();
      if (strlen($domain)) {
        $domain = ltrim($domain, ".");
        $subdomain = Text::removeFromEnd($host, "." . $domain);
        if ($subdomain != "www") {
          $uri = "/" . $subdomain . rtrim($uri, "/");
        }
      }
    }
    self::$uri = $uri;
    $publicPath = self::getPublicPath();
    $filePublic = file($publicPath . self::$uri);
    if (file($publicPath . self::$uri)) {
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
    [self::$file, self::$params] = Routes::mathRoutes($routes, self::$uri);
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
    self::init();
    if (strlen(self::$file)) {
      try {
        ob_start();
        self::includeFile();
        exit;
      } catch (\Throwable $th) {
        if (Env::getEnvOrValue('PKIT_CLEAR', 'true') == "true") {
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
      if (Env::getEnvOrValue('PKIT_DEBUG', null) == 'true') {
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
