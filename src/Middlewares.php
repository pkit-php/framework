<?php

namespace Pkit;

use Attribute;
use Pkit\Http\Request;
use Phutilities\Parse;
use Pkit\Throwable\Error;

#[Attribute]
class Middlewares
{
  private \Closure $controller;

  public static function filterMiddlewares(array |string $middlewares, string $method)
  {
    $middlewares = Parse::anyToArray($middlewares);

    $methodsMiddlewares = $middlewares[strtolower($method)] ?? [];
    $methodsMiddlewares = Parse::anyToArray($methodsMiddlewares);

    $methods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS', 'TRACE', 'HEAD'];
    $middlewares = array_filter($middlewares, function ($key) use ($methods) {
      return in_array($key, $methods) == false;
    }, ARRAY_FILTER_USE_KEY);

    return array_merge($middlewares, $methodsMiddlewares);
  }

  public function __construct(
    private array $middlewares,
  )
  {
  }

  public function setController(\Closure $controller)
  {
    $this->controller = $controller;
    return $this;
  }

  public function next(Request $request)
  {
    if (empty($this->middlewares)) {
      return call_user_func_array($this->controller, [$request]);
    }

    $firstKey = array_keys($this->middlewares)[0];
    if (is_numeric($firstKey)) {
      $middleware = $this->middlewares[$firstKey];
    }
    else {
      $params = $this->middlewares[$firstKey];
      $middleware = $firstKey;
    }
    unset($this->middlewares[$firstKey]);

    if (class_exists($middleware))
      $middleware = (new $middleware);

    $queue = $this;
    $next = function ($request) use ($queue) {
      return $queue->next($request);
    };

    if (!is_callable($middleware)) {
      if (is_string($middleware)) {
        throw new Error("Middlewares: $middleware not callable", 500);
      }
      throw new Error("Middlewares: middleware not callable", 500);
    }

    return $middleware($request, $next, $params ?? []);
  }
}