<?php

namespace Pkit\Http\Middleware;

use Pkit\Http\Request;
use Pkit\Http\Response;

class Queue
{
  private static $namespace = '\App\Middlewares';
  private array $middlewares;
  private \Closure $controller;

  public static function init(string $namespace)
  {
    self::$namespace = $namespace;
  }

  private static function getNamespace($class)
  {
    $path = explode("/", $class);
    $path = array_map(function ($value) {
      return ucfirst($value);
    }, $path);
    if (substr($class, 0, 4) === "pkit") {
      unset($path[0]);
      $baseClass = implode('\\', $path);
      return 'Pkit\\Http\\Middlewares\\' .  $baseClass;
    } else {
      $baseClass = implode('\\', $path);
      return self::$namespace . '\\' . $baseClass;
    }
  }

  public function __construct(
    $controller,
    $middlewares
  ) {
    $this->controller = $controller;
    $this->middlewares = $middlewares;
  }

  public function next(Request $request, Response $response)
  {
    if (empty($this->middlewares)) {
      return call_user_func_array($this->controller, [$request, $response]);
    }

    $middleware = array_shift($this->middlewares);

    $queue = $this;
    $next = function ($request, $response) use ($queue) {
      return $queue->next($request, $response);
    };

    $namespace = self::getNamespace($middleware);
    return (new $namespace())->handle($request, $response, $next);
  }
}
