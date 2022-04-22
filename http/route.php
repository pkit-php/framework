<?php namespace Pkit\Http;

use Pkit\Http\Middlewares;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Router;
use Pkit\Utils\Methods;

class Route
{
  public $middlewares;

  public function run()
  {
    $request = new Request(Router::$router);
    $response = new Response;
    $middlewares = Middlewares::getMiddlewares($this->middlewares ?? [], $request->getHttpMethod());

    (new Middlewares(function ($request, $response) {
      $this->runMethod($request, $response);
    }, $middlewares))->next($request, $response);
  }

  private function runMethod($request, $response)
  {
    $method = strtolower($request->getHttpMethod());
    if(method_exists($this, $method)){
      $this->$method($request, $response);
    } else {
      Methods::methodNotAllowed($request, $response);
    }
  }
}
