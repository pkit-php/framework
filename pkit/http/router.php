<?php

namespace Pkit\Http;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Middleware\Queue;
use Pkit\Utils\URI;
use Pkit\Utils\Routes;

// include __DIR__ . '/../utils/routes.php';
// include __DIR__ . '/../utils/uri.php';
// include __DIR__ . '/../utils/maths.php';

// include __DIR__ . '/request.php';
// include __DIR__ . '/response.php';
// include __DIR__ . '/middleware/index.php';


class Router
{
  private string
    $uri,
    $file,
    $routePath;

  private array $params = [];
  public static Router $router;

  public function __construct(string $routePath)
  {
    $this->uri = URI::sanitizeURI($_SERVER['REQUEST_URI']);
    $this->routePath = $routePath;
  }

  public function init()
  {
    $routes = Routes::getRoutes($this->routePath);
    [$this->file, $this->params] = Routes::mathRoute($routes, $this->getUri());
  }

  public function run()
  {
    if ($this->file) {
      setRouter($this);
      include $this->file;
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

  public function getFile()
  {
    return $this->file;
  }
}

function setRouter($router)
{
  Router::$router = $router;
}
