<?php

namespace Pkit\Http;

use Pkit\Http\Middlewares;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Router;

class Route
{
  public $middlewares;

  public function run()
  {
    [$request, $response] = Router::getRequestAndResponse();
    $middlewares = Middlewares::getMiddlewares($this->middlewares ?? [], $request->getHttpMethod());

    (new Middlewares(function ($request, $response) {
      $this->runMethod($request, $response);
    }, $middlewares))->next($request, $response);
  }

  private function runMethod(Request $request, Response $response)
  {
    $method = strtolower($request->getHttpMethod());
    if (method_exists($this, $method)) {
      $this->$method($request, $response);
    } else {
      $response->onlyCode()->notImplemented()->send();
    }
  }
}
