<?php

namespace Pkit\Http;

use Pkit\Http\Request;
use Pkit\Throwable\Error;
use Phutilities\Parse;

class Middlewares
{

  public static function filterMiddlewares(array|string $middlewares, string $method)
  {
    $middlewares = Parse::anyToArray($middlewares);

    $methodsMiddlewares = $middlewares[strtolower($method)] ?? [];
    $methodsMiddlewares = Parse::anyToArray($methodsMiddlewares);

    $methods = ['get', 'post', 'patch', 'put', 'delete', 'options', 'trace', 'head'];
    $middlewares = array_filter($middlewares, function ($key) use ($methods) {
      return in_array($key, $methods) == false;
    }, ARRAY_FILTER_USE_KEY);

    return array_merge($middlewares, $methodsMiddlewares);
  }

  public function __construct(
    private \Closure $controller,
    private array $middlewares,
  ) {
  }

  public function next(Request $request)
  {
    if (empty($this->middlewares)) {
      return call_user_func_array($this->controller, [$request]);
    }

    $firstKey = array_keys($this->middlewares)[0];
    if (is_numeric($firstKey)) {
      $middleware = $this->middlewares[$firstKey];
    } else {
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

    return $middleware($request, $next, $params ?? []);
  }
}
