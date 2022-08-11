<?php

namespace Pkit\Http;

use Pkit\Http\Request;
use Pkit\Throwable\Error;
use Pkit\Utils\Parse;

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

    $lastKeyMiddleware = array_key_last($this->middlewares);
    if (is_numeric($lastKeyMiddleware)) {
      $middleware = array_shift($this->middlewares);
    } else {
      $params = array_shift($this->middlewares);
      $middleware = $lastKeyMiddleware;
    }

    if (class_exists($middleware) == false)
      throw new Error(
        "Middlewares: Class '$middleware' not exists",
        Status::INTERNAL_SERVER_ERROR
      );

    if (method_exists($middleware, "handle") == false)
      throw new Error(
        "Middlewares: Class '$middleware' is not valid",
        Status::INTERNAL_SERVER_ERROR
      );

    $object = (new $middleware);

    if ($params != null)
      $params = Parse::anyToArray($params);

    $queue = $this;
    $next = function ($request) use ($queue) {
      return $queue->next($request);
    };

    return $object->handle($request, $next, $params ?? []);
  }
}
