<?php

namespace Pkit\Http\Router;

use Pkit\Http\ContentType;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Utils\View;

class Debug
{
  public static function log(Request $request, $message, $code)
  {
    $accepts = $request->headers['Accept'];
    if (strpos($accepts, 'text/html') !== false) {
      self::html($message, $code);
    } else if (strpos($accepts, 'application/json') !== false) {
      self::json($message, $code);
    } else {
      echo (new Response($message, $code));
      exit;
    }
  }

  public static function html($message, $code)
  {
    echo new Response(View::layout("pkit/code", [
      'code' => $code,
      'message' => $message,
    ]), $code);
    exit;
  }

  public static function json($message, $code)
  {
    echo (new Response([
      "message" => $message,
    "code" => $code]))
      ->contentType(ContentType::JSON);
    exit;
  }
}
