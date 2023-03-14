<?php

namespace Pkit\Http;

use Phutilities\Parse;
use Phutilities\Sanitize;

class Request
{
  private static ?Request $instance = null;
  public readonly string
  $httpMethod,
  $uri;
  public readonly mixed $postVars;
  public readonly array
  $headers,
  $queryParams,
  $cookies;

  public function __construct()
  {
    $this->httpMethod = $_SERVER['REQUEST_METHOD'];
    $this->queryParams = $_GET ?? [];
    $this->cookies = $_COOKIE ?? [];

    $this->headers = self::getHeaders();
    $this->uri = self::getUri();
    $this->postVars = self::getPostVars();
  }

  public static function getInstance(): self
  {
    if (!self::$instance) {
      self::$instance = new Request();
    }
    return self::$instance;
  }

  public static function getHeaders()
  {
    return getallheaders();
  }

  public static function getUri()
  {
    return Sanitize::uri($_SERVER["REQUEST_URI"]);
  }

  public static function getPostVars()
  {
    $contentType = trim(explode(';', @getallheaders()['Content-Type'])[0]);
    try {
      switch ($contentType) {
        case 'text/plain':
          return file_get_contents('php://input');
        case 'application/json':
          $inputRaw = file_get_contents('php://input');
          $json = json_decode($inputRaw, true);
          return $json;
        case 'application/xml':
          $xml = file_get_contents('php://input');
          return Parse::xmlToArray($xml);
        case 'application/x-www-form-urlencoded':
        case 'multipart/form-data':
        case null:
          return array_merge($_POST, $_FILES);
        default:
          exit(Response::code(Status::UNSUPPORTED_MEDIA_TYPE));
      }
    }
    catch (\Throwable $th) {
      exit(Response::code(Status::BAD_REQUEST));
    }
  }
}