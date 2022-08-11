<?php

namespace Pkit\Http\Router;

use Pkit\Http\ContentType;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Utils\View;
use Throwable;

class Debug
{
  public static function error(Request $request, Throwable $err)
  {
    $accepts = $request->headers['accept'];
    if (strpos($accepts, 'text/html') !== false) {
      self::html_err($err);
    } else if (
      strpos($accepts, 'application/json') !== false ||
      strpos($accepts, '*/*') !== false
      ) {
      self::json_err($err);
    } else {
      echo new Response($err, $err->getCode());
      exit;
    }
  }

  public static function html_err(Throwable $err)
  {
    echo new Response(View::layout("pkit/code", [
      'code' => $err->getCode(),
      'description' => $err->getMessage(), 
      'message' => $err->getMessage(),
      'title' => $err->getMessage(),
      "traces" => $err->getTrace(),
    ]), $err->getCode());
    exit;
  }

  public static function json_err(Throwable $err)
  {
    echo (new Response([
      "code" => $err->getCode(),
      "message" => $err->getMessage(),
      "trace" => $err->getTrace(),
  ],
    $err->getCode()))
      ->contentType(ContentType::JSON);
    exit;
  }
}
