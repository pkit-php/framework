<?php

namespace Pkit\Http;

use Pkit\Http\Router\Debug;
use Pkit\Http\Router\Routes;
use Pkit\Throwable\Error;
use Pkit\Throwable\Redirect;
use Pkit\Utils\Sanitize;
use Pkit\Utils\Env;
use Pkit\Utils\FS;
use Pkit\Utils\Text;
use ReflectionClass;
use ReflectionObject;

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
    $uri = Sanitize::uri($_SERVER['REQUEST_URI']);
    if (self::getSubDomain()) {
      $host = $request->headers['Host'];
      $subdomain = explode($host, ".")[0];
      if (
        $subdomain != "www" &&
        !is_numeric($subdomain) &&
        preg_match('/^\p{L}+$/u', $subdomain)
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

      if (str_ends_with($route, "/index.php")) {
        $route = Text::removeFromEnd($route, "/index.php");
        if ($route == "") {
          $route = "/";
        }
      } else {
        $route = Text::removeFromEnd($route, ".php");
      }

      $params = Routes::matchRouteAndParams($route, self::$uri);
      return is_array($params);
    }, true) ?? "";
    self::$params = $params;
    self::$especialRoute = self::$routePath . '/*.php';
  }

  private static function tryRunRoute(\Closure $function)
  {
    try {
      ob_start();
      $function();
    } catch (Error $err) {
      $code = $err->getCode();
    } catch (Redirect $red) {
      echo (new Response("", $red->getCode()))
        ->header("Location", $red->getMessage());
      exit;
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

  private static function runRoute(string $route, Request $request, ?Error $err = null)
  {
    $classes = get_declared_classes();
    include $route;
    $classes = array_diff(get_declared_classes(), $classes);

    $class = @array_values($classes)[0];
    if (is_null($class))
      exit;

    $parentClass = (new ReflectionClass($class))->getParentClass();
    if ($parentClass == false)
      exit;

    if (is_null($err))
      $typeRoute = "Route";
    else
      $typeRoute = "EspecialRoute";

    $parentClassName = $parentClass->name;
    if (
      $parentClassName == "Pkit\Http\\$typeRoute" ||
      $parentClassName == "Pkit\Abstracts\\$typeRoute"
    ) {
      echo $class::run($request, $err);
    }
    exit;
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

    $mime_types = [
      "css" => "text/css"
    ];

    if (($mime_content = @$mime_types[$extension])
      || ($mime_content = mime_content_type(self::$file))
    ) {
      echo (new Response($content))->contentType($mime_content);
    } else {
      echo new Response("", Status::UNSUPPORTED_MEDIA_TYPE);
    }
    exit;
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
      if ($extension != 'php') {
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

    if (Env::getEnvOrValue('PKIT_DEBUG', "false") == 'true')
      echo Debug::error($request, $err);
    else
      echo new Response("", $err->getCode());
    exit;
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
