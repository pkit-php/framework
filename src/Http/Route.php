<?php

namespace Pkit\Http;

use Pkit\Http\Middlewares;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Router;

class Route
{
  public $middlewares;

  public static function run()
  {
    [$request, $response] = Router::getRequestAndResponse();

    $class = static::class;
    $route = new $class;

    $middlewares = Middlewares::getMiddlewares($route->middlewares ?? [], $request->httpMethod);

    (new Middlewares(function ($request, $response) use ($route) {
      $route->runMethod($request, $response);
    }, $middlewares))->next($request, $response);
  }

  public function runMethod(Request $request, Response $response)
  {
    $all = 'all';
    if (method_exists($this, $all)) {
      $this->$all($request, $response);
    }
    $method = strtolower($request->httpMethod);
    $methods = ['get', 'post', 'patch', 'put', 'delete', 'options', 'trace', 'head'];
    if (in_array($method, $methods)) {
      $this->$method($request, $response);
    } else {
      $response->onlyCode()->setStatus(Status::NOT_IMPLEMENTED);
      $response->send();
    }
  }
}
