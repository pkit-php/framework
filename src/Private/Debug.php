<?php

namespace Pkit\Private;

use Pkit\Http\ContentType;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Utils\View;

class Debug
{
  public static function log(Request $request, Response $response, $message)
  {
    $accepts = $request->headers['Accept'];
    if (strpos($accepts, 'text/html') !== false) {
      self::html($response, $message);
    } else if (strpos($accepts, 'application/json') !== false) {
      self::json($response, $message);
    } else {
      $response->send($message);
    }
  }

  public static function html(Response $response, $message)
  {
    $response->contentType(ContentType::HTML);
    $response->send(View::layout("pkit/code", [
      'code' => $response->status(),
      'message' => $message,
    ]));
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
