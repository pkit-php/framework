<?php

class Queue
{

  private static $map = [];
  private
    $middlewares,
    $controller;

  public static function setMap(array $map)
  {
    self::$map = $map;
  }

  public function __construct(
    $controller,
    $middlewares
  ) {
    $this->controller = $controller;
    $this->middlewares = $middlewares;
  }

  public function next($request, $response)
  {
    if (empty($this->middlewares)) {
      return call_user_func_array($this->controller, [$request, $response]);
    }

    $middleware = array_shift($this->middlewares);

    if (!isset(self::$map[$middleware])) {
      return $response->error(500)->send();
    }

    $queue = $this;
    $next = function ($request, $response) use ($queue) {
      return $queue->next($request, $response);
    };

    return (new self::$map[$middleware])->handle($request, $response, $next);
  }
}
