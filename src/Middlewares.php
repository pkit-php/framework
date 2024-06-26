<?php

namespace Pkit;

use Attribute;
use Pkit\Exceptions\Http\Status\InternalServerError;
use Pkit\Http\Request;

#[Attribute]
class Middlewares
{
  private \Closure $controller;

  public static function filterMiddlewares(array|string $middlewares, string $method)
  {
    $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];

    $methodsMiddlewares = $middlewares[strtolower($method)] ?? [];
    $methodsMiddlewares = is_array($methodsMiddlewares) ? $methodsMiddlewares : [$methodsMiddlewares];

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
        throw new InternalServerError("Middleware $middleware not callable");
      }
      throw new InternalServerError("Middleware not callable");
    }

    return $middleware($request, $next, $params ?? []);
  }
}