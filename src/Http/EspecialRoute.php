<?php

namespace Pkit\Http;

use Pkit\Http\Request;
use Pkit\Http\Route\Base;
use Throwable;

class EspecialRoute extends Base
{
  public static function run(Request $request, Throwable $err)
  {
    $class = static::class;
    $route = new $class;

    if ($method = $route->getMethod($request)) {
      return $route->$method($request, $err);
    }

    throw $err;
  }
}
