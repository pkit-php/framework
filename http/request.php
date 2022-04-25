<?php

namespace Pkit\Http;

use Pkit\Http\Router;
use Pkit\Utils\Converter;

class Request
{
  private string $httpMethod;
  private Router $router;
  private array
    $headers = [],
    $queryParams = [];
  private mixed $postVars;

  public function __construct(Router $router)
  {
    $this->router = $router;

    $this->httpMethod = $_SERVER['REQUEST_METHOD'];
    $this->queryParams = $_GET ?? [];

    $this->headers = getallheaders();
    if ($this->httpMethod != 'GET') {
      $this->setPostVars();
    }
  }

  private function setPostVars()
  {
    $contentType = $this->getHeader('content-type');
    try {
      switch ($contentType) {
        case 'application/json':
          $inputRaw = file_get_contents('php://input');
          $this->postVars = json_decode($inputRaw, true);
          break;
        case 'application/xml':
          $xml = file_get_contents('php://input');
          $this->postVars = Converter::xmlToArray($xml);
          break;
        case 'application/x-www-form-urlencoded':
          $this->postVars = $_POST;
          break;
        case 'application/form-data':
          $this->postVars = $_POST;
          break;
        case null:
          $this->postVars = $_POST;
          break;
        default:
          (new Response)->unsupportedMediaType()->send();
          break;
      }
    } catch (\Throwable $th) {
      (new Response)->badRequest()->send();
    }
  }

  public function getRouter()
  {
    return $this->router;
  }

  public function getPostVars()
  {
    return $this->postVars;
  }

  public function getQueryParams()
  {
    return $this->queryParams;
  }

  public function getHeaders()
  {
    return $this->headers;
  }

  public function getHeader($header)
  {
    return $this->headers[$header];
  }

  public function getHttpMethod()
  {
    return $this->httpMethod;
  }
}
