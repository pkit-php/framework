<?php

namespace Pkit\Http;

use Pkit\Abstracts\Route as AbstractsRoute;
use Pkit\Http\Middleware\Queue;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Router;
use Pkit\Utils\Methods;

class Route extends AbstractsRoute
{

  private function getMiddlewares($middlewares, $method)
  {
    $newMiddlewares = [];
    foreach ($middlewares as $key => $middleware) {
      if (is_int($key)) {
        $newMiddlewares[] = $middleware;
      }
    }
    $methodsMiddlewares = $middlewares[strtolower($method)] ?? [];
    return array_merge($newMiddlewares, $methodsMiddlewares);
  }

  public function run()
  {
    $request = new Request(Router::$router);
    $response = new Response;

    $middlewares = $this->getMiddlewares($this->middlewares ?? [], $request->getHttpMethod());

    (new Queue(function ($request, $response) {
      $this->runMethod($request, $response);
    }, $middlewares))->next($request, $response);
  }

  private function runMethod($request, $response)
  {

    switch ($request->getHttpMethod()) {
      case 'GET':
        $this->get($request, $response);
        break;
      case 'HEAD':
        $this->head($request, $response);
        break;
      case 'PUT':
        $this->put($request, $response);
        break;
      case 'POST':
        $this->post($request, $response);
        break;
      case 'PATCH':
        $this->patch($request, $response);
        break;
      case 'TRACE':
        $this->trace($request, $response);
        break;
      case 'OPTIONS':
        $this->options($request, $response);
        break;
      case 'DELETE':
        $this->delete($request, $response);
        break;
      default:
        Methods::methodNotAllowed($request, $response);
        break;
    }
  }
}
