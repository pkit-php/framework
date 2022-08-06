<?php

namespace Pkit\Http;

use Pkit\Http\Middlewares;
use Pkit\Http\Request;

class Route
{
  public $middlewares;

  public static function run(Request $request)
  {
    $class = static::class;
    $route = new $class;

    $middlewares = Middlewares::filterMiddlewares($route->middlewares ?? [], $request->httpMethod);

    return (new Middlewares(function ($request) use ($route) {
      return $route->runMethod($request);
    }, $middlewares))->next($request);
  }

  public function runMethod(Request $request)
  {
    $all = 'all';
    if (method_exists($this, $all)) {
      $return = $this->$all($request);
      if($return)
        return $return;
    }
    $method = strtolower($request->httpMethod);
    $methods = ['get', 'post', 'patch', 'put', 'delete', 'options', 'trace', 'head'];
    if (in_array($method, $methods) && method_exists($this, $method)) {
      return $this->$method($request);
    } else {
      return new Response("", Status::NOT_IMPLEMENTED);
    }
  }
}
