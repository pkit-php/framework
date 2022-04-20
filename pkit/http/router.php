<?php

namespace Pkit\Http;

use Pkit\Utils\Routes;
use Pkit\Utils\Sanitize;

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
    $this->uri = Sanitize::sanitizeURI($_SERVER['REQUEST_URI']);
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
      self::$router = $this;
      include $this->file;

      $extension = '.' . @end(explode('.', Router::$router->getFile()));
      if ($extension != '.php') {
        (new Response)
          ->setContentType(mime_content_type($extension) ?? "")
          ->send();
        exit;
      }
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
