<?php

namespace Pkit\Utils;

use Pkit\Http\ContentType;
use Pkit\Http\Request;
use Pkit\Http\Response;

class Debug
{
  public static function log(Request $request, Response $response, $message)
  {
    $accepts = $request->headers['Accept'];
    if (strpos($accepts, 'text/html') !== false) {
      self::html($response, $message);
    } else if (strpos($accepts, 'application/json') !== true) {
      self::json($response, $message);
    } else {
      echo $message;
    }
  }

  public static function html(Response $response, $message)
  {
    $response->contentType(ContentType::HTML)->send(
      '<pre>' . json_encode([
        'code' => $response->status(),
        'error' => $message,
      ]) . '</pre>'
    );
  }

  public static function json(Response $response, $message)
  {
    $response->contentType(ContentType::JSON)->send(
      [
        'code' => $response->status(),
        'error' => $message,
      ]
    );
  }
}