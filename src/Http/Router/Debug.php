<?php

namespace Pkit\Http\Router;

use Pkit\Http\ContentType;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Utils\Parse;
use Pkit\Utils\View;
use Throwable;

class Debug
{
  public static function error(Request $request, Throwable $err): Response
  {
    $accepts = Parse::headerToArray($request->headers['accept'], false);
    if (in_array('text/html', $accepts)) {
      return self::html_err($err);
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
    return new Response(View::layout("pkit/code", [
      'code' => $err->getCode(),
      'description' => $err->getMessage(), 
      'message' => $err->getMessage(),
      'title' => $err->getMessage(),
      "traces" => $err->getTrace(),
    ]), $err->getCode());
  }

  public static function json_err(Throwable $err): Response
  {
    return (new Response([
      "code" => $err->getCode(),
      "message" => $err->getMessage(),
      "trace" => $err->getTrace(),
  ],
    $err->getCode()))
      ->contentType(ContentType::JSON);
  }
}
