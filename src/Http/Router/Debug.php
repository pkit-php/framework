<?php

namespace Pkit\Http\Router;

use Phutilities\Env;
use Pkit\Http\ContentType;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Phutilities\Parse;
use Throwable;

class Debug
{
  private static ?bool $canTraces = null;

  private static function getCanTraces()
  {
    if (is_null(self::$canTraces)){
      self::$canTraces = 
      Env::getEnvOrValue("PKIT_TRACES", "true") == "true";
    }
    return self::$canTraces;
  }

  public static function error(Request $request, Throwable $err): Response
  {
    $accepts = Parse::headerToArray($request->headers['accept'], false);
    if (in_array('text/html', $accepts)) {
      return self::html_err($err);
    } else if (in_array('application/xml', $accepts)) {
      return self::xml_err($err);
    } else if (
      in_array('application/json', $accepts ) ||
      in_array('*/*',$accepts)) {
      return self::json_err($err);
    } else {
      return new Response($err, $err->getCode());
    }
  }

  public static function html_err(Throwable $err): Response
  {
    return Response::render("pkit/code", $err->getCode(), [
      'code' => $err->getCode(),
      'description' => $err->getMessage(), 
      'message' => $err->getMessage(),
      'title' => $err->getMessage(),
      "traces" => self::getCanTraces() ? $err->getTrace() : null,
    ]);
  }

  public static function json_err(Throwable $err): Response
  {
    return (new Response(array_filter([
      "code" => $err->getCode(),
      "message" => $err->getMessage(),
      "trace" => self::getCanTraces() ? $err->getTrace() : null,
  ]),
    $err->getCode()))
      ->contentType(ContentType::JSON);
  }

  public static function xml_err(Throwable $err): Response
  {
    return (new Response(
      array_filter([
        "code" => $err->getCode(),
        "message" => $err->getMessage(),
        "trace" => self::getCanTraces() ? $err->getTrace() : null,
      ]),
      $err->getCode()
    ))
      ->contentType(ContentType::XML);
  }
}
