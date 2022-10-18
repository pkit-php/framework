<?php

namespace Pkit\Http;

use Pkit\Http\Router\Debug;
use Pkit\Http\Router\Routes;
use Pkit\Throwable\Error;
use Pkit\Throwable\Redirect;
use Phutilities\Sanitize;
use Phutilities\Env;
use Phutilities\FS;
use Phutilities\Text;
use ReflectionObject;
use Throwable;

class Router
{
  private static string
    $uri,
    $file;

  private static array $params = [];
  private static ?bool $subDomain = null;
  private static ?string
    $especialRoute = null,
    $routePath = null,
    $publicPath = null;

  public static function config(string $routePath, ?string $publicPath = null, bool $subDomain = false)
  {
    self::$routePath = rtrim($routePath, "/");
    self::$publicPath = $publicPath
      ? rtrim($publicPath, "/")
      : null;
    self::$subDomain = $subDomain;
  }

  public static function getRoutePath()
  {
    if (is_null(self::$routePath))
      self::$routePath = Env::getEnvOrValue("ROUTES_PATH", $_SERVER["DOCUMENT_ROOT"] . "/routes");
    return self::$routePath;
  }

  public static function getPublicPath()
  {
    if (is_null(self::$publicPath))
      self::$publicPath = Env::getEnvOrValue("PUBLIC_PATH", $_SERVER["DOCUMENT_ROOT"] . "/public");
    return self::$publicPath;
  }

  public static function getSubDomain()
  {
    if (is_null(self::$subDomain))
      self::$subDomain = Env::getEnvOrValue("SUB_DOMAIN", "false") == "true";
    return self::$subDomain;
  }


  private static function init(Request $request)
  {
    $uri = Sanitize::uri($_SERVER["REQUEST_URI"]);
    if (self::getSubDomain()) {
      $host = $request->headers["Host"];
      $subdomain = explode($host, ".")[0];
      if (
        $subdomain != "www" &&
        !is_numeric($subdomain) &&
        preg_match("/^\p{L}+$/u", $subdomain)
      )
        $uri = "/" . $subdomain . rtrim($uri, "/");
    }
    self::$uri = $uri;
    $filePublic = self::getPublicPath() . str_replace("/../", "/", self::$uri);
    if (@file($filePublic)) {
      self::$file = $filePublic;
      return;
    }
    self::setFileAndParams();
  }

  private static function setFileAndParams()
  {
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

  private static function tryRunRoute(\Closure $function)
  {
    try {
      ob_start();
      $function();
    } catch (Error $err) {
      $code = $err->getCode();
    } catch (Redirect $red) {
      exit((new Response("", $red->getCode()))
        ->header("Location", $red->getMessage()));
    } catch (\Throwable $err) {
      $code = Status::validate($err->getCode())
        ? $err->getCode()
        : 500;
      $codeProperty = (new ReflectionObject($err))
        ->getProperty("code");
      $codeProperty->setAccessible(true);
      $codeProperty->setValue($err, $code);
    } finally {
      if (Env::getEnvOrValue('PKIT_CLEAR', 'true') == "true") {
        ob_end_clean();
      }
      return $err ?? new Error(
        "Router: status and message is null or invalid",
        Status::INTERNAL_SERVER_ERROR
      );
    }
  }

  private static function runRoute(string $route, Request $request, ?Throwable $err = null)
  {
    $return = include $route;
    if (
      is_string($return) ||
      (is_object($return) && method_exists($return, "__toString"))
    )
      exit($return);

    if (is_callable($return) == false && Env::getEnvOrValue("PKIT_RETURN", "true") == "true")
      throw new Error("The route $route was not a valid return", 500);

    if ($return === 1 || is_null($return))
      exit;

    exit($return($request, $err));
  }

  private static function includeFile()
  {
    ob_start();
    include self::$file;
    return ob_get_clean() ?? "";
  }

  private static function sendMimeFile(string $extension)
  {
    $content = self::includeFile();

    if (($mime_content = ContentType::getContentType($extension))
      || ($mime_content = mime_content_type(self::$file))
    )
      exit((new Response($content))
        ->header("Content-Type", $mime_content));
    else
      exit(new Response("", Status::UNSUPPORTED_MEDIA_TYPE));
  }

  public static function getExtension($file)
  {
    $explode = explode(".", $file);
    if (str_starts_with($file, ".") == false)
      unset($explode[0]);
    return implode(".", $explode);
  }

  public static function run()
  {
    $request = new Request;
    self::init($request);
    if (strlen(self::$file)) {
      $extension = self::getExtension(self::$file);
      if ($extension != "php") {
        self::sendMimeFile($extension);
      } else {
        $err = self::tryRunRoute(function () use ($request) {
          self::runRoute(self::$file, $request);
        });
      }
    } else {
      $err = new Error(
        "page '" . self::$uri . "' not found",
        Status::NOT_FOUND
      );
    }

    if (@file(self::$especialRoute))
      $err = self::tryRunRoute(function () use ($request, $err) {
        self::runRoute(self::$especialRoute, $request, $err);
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
}
