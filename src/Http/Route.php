<?php

namespace Pkit\Http;

use Pkit\Http\Route\Base;
use Pkit\Http\Middlewares;
use Pkit\Http\Request;

class Route extends Base
{
  public $middlewares = [];

  public static function run(Request $request)
  {
    $class = static::class;
    $route = new $class;

    if ($method = $route->getMethod($request)) {

      $middlewares = Middlewares::filterMiddlewares(
        $route->middlewares,
        $request->httpMethod
      );
      return (new Middlewares(function ($request) use ($route, $method) {
        return $route->$method($request);
      }, $middlewares))->next($request);
    }

    return new Response("", Status::NOT_IMPLEMENTED);
  }
}
