<?php

include __DIR__ . '/../utils/routes.php';
include __DIR__ . '/../utils/uri.php';
include __DIR__ . '/../utils/maths.php';

include __DIR__ . '/request.php';
include __DIR__ . '/response.php';
include __DIR__ . '/middleware/index.php';


class Router
{
  private string
    $uri,
    $file,
    $routePath;

  private Request $request;
  private Response $response;
  private array $params = [];

  public function __construct(string $routePath)
  {
    $this->uri = sanitizeURI($_SERVER['REQUEST_URI']);
    $this->request = new Request($this);
    $this->response = new Response;
    $this->routePath = $routePath;
  }

  private function getMiddlewares($middlewares)
  {
    $newMiddlewares = [];
    foreach ($middlewares as $key => $middleware) {
      if (is_int($key)) {
        $newMiddlewares[] = $middleware;
      }
    }
    $methodsMiddlewares = $middlewares[strtolower($this->request->getHttpMethod())] ?? [];
    return array_merge($newMiddlewares, $methodsMiddlewares);
  }

  public function init()
  {
    $routes = getRoutes($this->routePath);
    [$this->file, $this->params] = mathRoute($routes, $this->getUri());
  }

  public function run()
  {
    if ($this->file) {
      include $this->file;
      $route = export();

      $middlewares = $this->getMiddlewares($route->middlewares ?? []);
      $response = $this->response;
      $request = $this->request;

      (new Queue(function ($request, $response) use ($route) {
        $this->runMethod($route, $request, $response);
      }, $middlewares))->next($request, $response);
    } else {
      $this->response
        ->onlyCode()
        ->notFound()
        ->send();
    }
  }


  public function getUri()
  {
    return $this->uri;
  }

  public function getParams()
  {
    return $this->params;
  }

  private function runMethod($route, $request, $response)
  {

    switch ($request->getHttpMethod()) {
      case 'GET':
        $route->get($request, $response);
        break;
      case 'HEAD':
        $route->head($request, $response);
        break;
      case 'PUT':
        $route->put($request, $response);
        break;
      case 'POST':
        $route->post($request, $response);
        break;
      case 'PATCH':
        $route->patch($request, $response);
        break;
      case 'TRACE':
        $route->trace($request, $response);
        break;
      case 'OPTIONS':
        $route->options($request, $response);
        break;
      case 'DELETE':
        $route->delete($request, $response);
        break;
      default:
        include __DIR__ . '/../utils/methodNotAllowed.php';
        methodNotAllowed($request, $response);
        break;
    }
  }
}
