<?php

namespace Pkit\Http;

use Pkit\Http\Middlewares;
use Pkit\Http\Request;
use ReflectionClass;

class Route
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

  public function getMethod(Request $request)
  {
    $all = 'all';
    if (method_exists($this, $all)) {
      if ((new ReflectionClass($this))
        ->getMethod($all)
        ->getDocComment() !== "/** @abstract */"
      ) {
        return $all;
      }
    }
    $method = strtolower($request->httpMethod);
    $methods = ['get', 'post', 'patch', 'put', 'delete', 'options', 'trace', 'head'];
    if (
      in_array($method, $methods) &&
      method_exists($this, $method) &&
      (new ReflectionClass($this))
      ->getMethod($method)
      ->getDocComment() !== "/** @abstract */"
    ) {
      return $method;
    }
    return false;
  }
}
