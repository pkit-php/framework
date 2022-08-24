<?php

namespace Pkit\Http\Router;

use Pkit\Http\ContentType;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Utils\View;
use Phutilities\Parse;
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
    return Response::render("pkit/code", $err->getCode(), [
      'code' => $err->getCode(),
      'description' => $err->getMessage(), 
      'message' => $err->getMessage(),
      'title' => $err->getMessage(),
      "traces" => $err->getTrace(),
    ]);
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
