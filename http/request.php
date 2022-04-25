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
    $queryParams = [],
    $postVars = [];

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
    $contentType = trim(explode(';', $this->getHeader('content-type'))[0]);
    try {
      switch ($contentType) {
        case 'application/json':
          $inputRaw = file_get_contents('php://input');
          $json = json_decode($inputRaw, true);
          if (!is_array($json)) {
            $this->postVars[] = $json;
            break;
          }
          $this->postVars = $json;
          break;
        case 'application/xml':
          $xml = file_get_contents('php://input');
          $this->postVars = Converter::xmlToArray($xml);
          break;
        case 'application/x-www-form-urlencoded':
        case 'multipart/form-data':
        case null:
          $this->postVars = $_POST;
          break;
        default:
          (new Response)->unsupportedMediaType()->onlyCode()->send();
          break;
      }
    } catch (\Throwable $th) {
      (new Response)->badRequest()->onlyCode()->send($th);
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
    return $this->headers[lcfirst($header)];
  }

  public function getHttpMethod()
  {
    return $this->httpMethod;
  }
}
