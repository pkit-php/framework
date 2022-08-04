<?php

namespace Pkit\Http;

use Pkit\Private\Debug;
use Pkit\Private\Routes;
use Pkit\Utils\Sanitize;
use Pkit\Utils\Env;
use Pkit\Utils\FS;
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
    $uri = Sanitize::uri($_SERVER['REQUEST_URI']);
    if (self::getSubDomain()) {
      $host = self::$request->headers['Host'];
      $domain = self::getDomain();
      if (strlen($domain)) {
        $domain = ltrim($domain, ".");
        $subdomain = Text::removeFromEnd($host, "." . $domain);
        if ($subdomain != "www") {
          $uri = "/" . trim($subdomain, "/") . rtrim($uri, "/");
        }
      }
    }
    self::$uri = $uri;
    $publicPath = self::getPublicPath();
    $filePublic = $publicPath . str_replace("/../", "/", self::$uri);
    if (@file($filePublic)) {
      self::$file = $filePublic;
    } 
    else {
      self::setFileAndParams();
    }
  }

  private static function setFileAndParams()
  {
    $params = [];
    self::$file = FS::someFile(self::getRoutePath(), function ($file) use ($params) {
      $file = Text::removeFromStart($file, self::$routePath);
      $file = Text::removeFromEnd($file, ".php");
      $file = Text::removeFromEnd($file, "index");
      $file = "/".trim($file, "/");
      
      $params = Routes::matchRouteAndParams($file, self::$uri);

      return is_array($params);
    }, true) ?? "";
    self::$params = $params;
    self::$especialRoute = self::$routePath . '/*';
  }

  private static function includeFile()
  {
    include self::$file;

    $extension = @end(explode(".", self::$file));

    $mime_types = [
      "css" => "text/css"
    ];

    if ($extension != '.php') {
      if (($mime_content = @$mime_types[$extension]) || ($mime_content = mime_content_type(self::$file))) {
        self::$response->contentType($mime_content);
      } else {
        self::$response->status(Status::INTERNAL_SERVER_ERROR);
      }
      self::$response->send();
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
        self::$response->status(is_string($code) ? 500 : $code);
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
      try {
        ob_start();
        include self::$especialRoute;
        exit;
      } catch (\Throwable $th) {
        if (Env::getEnvOrValue('PKIT_CLEAR', 'true') == "true") {
          ob_end_clean();
        }
        self::$response->status($th->getCode());
        self::$message = $th->getMessage();
      }
    }
    self::runDebugIfPossible();
  }

  private static function runDebugIfPossible()
  {
    if (Env::getEnvOrValue('PKIT_DEBUG', null) == 'true') {
      Debug::log(self::$request, self::$response, self::$message);
    } else {
      self::$response->send();
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
