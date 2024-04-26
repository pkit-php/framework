<?php

namespace Pkit\Http;

use Pkit\Utils\Parser;

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
    $uri = $_SERVER["REQUEST_URI"];
    $uri = urldecode(parse_url($uri, PHP_URL_PATH));
    return $uri != "/" ? rtrim($uri, "/") : $uri;
  }

  public static function getPostVars()
  {
    if ($contentType = @getallheaders()['Content-Type']) {
      $contentType = Parser::headerToArray($contentType, false)[0];
    } else {
      return [];
    }
    if (is_null($contentType))
      return null;
    $contentType = trim(explode(';', $contentType)[0]);
    try {
      switch ($contentType) {
        case 'text/plain':
          return file_get_contents('php://input');
        case 'application/json':
          $inputRaw = file_get_contents('php://input');
          $json = json_decode($inputRaw, true, 512, JSON_THROW_ON_ERROR);
          return $json;
        case 'application/xml':
          $xml = file_get_contents('php://input');
          return Parser::xmlToArray($xml);
        case 'application/x-www-form-urlencoded':
        case 'multipart/form-data':
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