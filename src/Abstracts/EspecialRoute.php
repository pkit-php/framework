<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\EspecialRoute as HttpEspecialRoute;


abstract class EspecialRoute extends HttpEspecialRoute
{
  public function options(Request $_, string $message, int $code)
  {
    return methodNotAllowed();
  }
  public function delete(Request $_, string $message, int $code)
  {
    return methodNotAllowed();
  }
  public function patch(Request $_, string $message, int $code)
  {
    return methodNotAllowed();
  }
  public function trace(Request $_, string $message, int $code)
  {
    return methodNotAllowed();
  }
  public function post(Request $_, string $message, int $code)
  {
    return methodNotAllowed();
  }
  public function head(Request $_, string $message, int $code)
  {
    return (new Response(""))
      ->header('Accept',
    'application/x-www-form-urlencoded, ' .
    'application/json, ' .
    'application/xml, ' .
    'multipart/form-data');
  }
  public function get(Request $_, string $message, int $code)
  {
    return methodNotAllowed();
  }
  public function put(Request $_, string $message, int $code)
  {
    return methodNotAllowed();
  }
  public function all(Request $_, string $message, int $code)
  {
  }
}
