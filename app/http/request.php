<?php

class Request
{
  private
    $httpMethod,
    $headers,
    $router,
    $queryParams = [],
    $postVars = [];

  public function __construct($router)
  {
    $this->router = $router;

    $this->httpMethod = $_SERVER['REQUEST_METHOD'];
    $this->queryParams = $_GET ?? [];

    $this->headers = getallheaders();
    $this->setPostVars();
  }

  private function setPostVars()
  {
    if ($this->httpMethod == 'GET') {
      return false;
    }

    $this->postVars = $_POST ?? [];

    $inputRaw = file_get_contents('php://input');

    $this->postVars = strlen($inputRaw) && empty($_POST) ? json_decode($inputRaw, true) : $this->postVars;;
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
