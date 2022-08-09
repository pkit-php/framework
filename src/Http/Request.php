<?php

namespace Pkit\Http;

use Pkit\Utils\Converter;

class Request
{
  public readonly string $httpMethod;
  public readonly array
    $headers,
    $queryParams,
    $postVars,
    $cookies;

  public function __construct()
  {
    $this->httpMethod = $_SERVER['REQUEST_METHOD'];
    $this->queryParams = $_GET ?? [];
    $this->cookies = $_COOKIE ?? [];

    $this->headers = getallheaders();
    if ($this->httpMethod != 'GET') {
      $this->setPostVars();
    }
  }

  private function setPostVars()
  {
    $contentType = trim(explode(';', @$this->headers['content-type'])[0]);
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
          echo new Response("", Status::UNSUPPORTED_MEDIA_TYPE);
          exit;
      }
    } catch (\Throwable $th) {
      echo new Response("",Status::BAD_REQUEST);
      exit;
    }
  }
}
