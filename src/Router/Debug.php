<?php

namespace Pkit\Router;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Phantom;
use Pkit\Utils\Parser;
use Pkit\View;
use ReflectionClass;
use Throwable;

class Debug
{
  private static ?bool $canTraces = null;

  private static function getCanTraces()
  {
    if (is_null(self::$canTraces)) {
      self::$canTraces =
        (getenv("PKIT_TRACES") ?: "true") == "true";
    }
    return self::$canTraces;
  }

  public static function error(Request $request, Throwable $err): Response
  {
    $accepts = Parser::headerToArray(@$request->headers['Accept'] ?? "", false);
    if (in_array('text/html', $accepts)) {
      return self::html_err($err);
    } else if (in_array('application/xml', $accepts)) {
      return self::xml_err($err);
    } else if (
      in_array('application/json', $accepts) ?:
      in_array('*/*', $accepts)
    ) {
      return self::json_err($err);
    } else {
      return new Response($err, $err->getCode());
    }
  }

  public static function html_err(Throwable $err): Response
  {
    $nameClass = (new ReflectionClass($err))->getShortName();
    $nameClass = preg_replace("([A-Z][^A-Z])", " $0", $nameClass);
    $nameClass = trim($nameClass);
    $render = Phantom::renderView(new View(__DIR__ . "/../pkit"), "code", [
      'name' => $nameClass,
      'code' => $err->getCode(),
      'message' => $err->getMessage(),
      'description' => $err->getMessage(),
      'title' => $err->getMessage(),
      "traces" => self::getCanTraces() ? $err->getTrace() : null,
    ]);
    return new Response($render, $err->getCode());
  }

  public static function json_err(Throwable $err): Response
  {
    return Response::json(
      array_filter([
        "code" => $err->getCode(),
        "message" => $err->getMessage(),
        "trace" => self::getCanTraces() ? $err->getTrace() : null,
      ]),
      $err->getCode()
    );
  }

  public static function xml_err(Throwable $err): Response
  {
    return Response::xml(
      array_filter([
        "code" => $err->getCode(),
        "message" => $err->getMessage(),
        "trace" => self::getCanTraces() ? $err->getTrace() : null,
      ]),
      $err->getCode()
    );
  }
}