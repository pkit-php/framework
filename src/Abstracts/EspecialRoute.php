<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\EspecialRoute as HttpEspecialRoute;
use Throwable;

abstract class EspecialRoute extends HttpEspecialRoute
{
  public function options(Request $_, Throwable $err)
  {
    return methodNotAllowed();
  }
  public function delete(Request $_, Throwable $err)
  {
    return methodNotAllowed();
  }
  public function patch(Request $_, Throwable $err)
  {
    return methodNotAllowed();
  }
  public function trace(Request $_, Throwable $err)
  {
    return methodNotAllowed();
  }
  public function post(Request $_, Throwable $err)
  {
    return methodNotAllowed();
  }
  public function head(Request $_, Throwable $err)
  {
    return (new Response(""))
      ->header('Accept',
    'application/x-www-form-urlencoded, ' .
    'application/json, ' .
    'application/xml, ' .
    'multipart/form-data');
  }
  public function get(Request $_, Throwable $err)
  {
    return methodNotAllowed();
  }
  public function put(Request $_, Throwable $err)
  {
    return methodNotAllowed();
  }
  public function all(Request $_, Throwable $err)
  {
  }
}
