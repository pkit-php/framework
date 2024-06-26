<?php

namespace Pkit\Http;

use Pkit\Exceptions\Http\Status\InternalServerError;
use Pkit\Utils\Parser;
use Pkit\Phantom;

class Response
{
  private array $headers = [];
  private array $cookies = [];
  private int $status;
  private array|string|object $content;

  const CONTENT_TYPE_SUPPORT = [
    ContentType::JSON,
    ContentType::HTML,
    ContentType::XML,
  ];

  public function __construct(array|string|object $content, int $status = 200)
  {
    $this->content = $content;
    $this->status($status);
  }

  public function header(string $key, string $value)
  {
    $this->headers[$key] = $value;
    return $this;
  }

  public function cookie(
    string $name,
    $value = "",
    $expires_or_options = 0,
    $path = "",
    $domain = "",
    $secure = false,
    $httponly = false
  )
  {
    $this->cookies[$name] = [
      "value"              => $value,
      "expires_or_options" => $expires_or_options,
      "path"               => $path,
      "domain"             => $domain,
      "secure"             => $secure,
      "httponly"           => $httponly
    ];
    return $this;
  }

  public function contentType(string $contentType)
  {
    if (in_array($contentType, self::CONTENT_TYPE_SUPPORT) == false)
      throw new InternalServerError(
        "Content-type $contentType not supported for conversion in response",
      );

    $this->headers['Content-Type'] = $contentType;
    return $this;
  }

  public function status(int $statusCode): self
  {
    if (!Status::validate($statusCode))
      throw new InternalServerError(
        "Status '$statusCode' is not valid in response",
      );
    $this->status = $statusCode;
    return $this;
  }

  public static function render(string $file, int $status = 200, array $args = [])
  {
    $render = Phantom::render($file, $args);

    return (new Response($render, $status))
      ->contentType(ContentType::HTML);
  }

  public static function json(array|string $content, int $status = 200)
  {
    return (new Response($content, $status))
      ->contentType(ContentType::JSON);
  }

  public static function xml(array|string $content, int $status = 200)
  {
    return (new Response($content, $status))
      ->contentType(ContentType::XML);
  }

  public static function mimeFile($filepath): Response
  {
    $content = file_get_contents($filepath);
    $extension = pathinfo($filepath)["extension"];

    if (
      ($mime_content = ContentType::getContentType($extension))
      || ($mime_content = mime_content_type($filepath))
    )
      return (new Response($content))
        ->header("Content-Type", $mime_content);
    else
      return Response::code(Status::UNSUPPORTED_MEDIA_TYPE);
  }

  public static function code(int $status)
  {
    return new Response("", $status);
  }

  public static function empty()
  {
    return new Response("");
  }

  private function fixContentType()
  {
    if (@$this->headers['Content-Type'])
      return;

    if (is_string($this->content)) {
      $this->headers['Content-Type'] = ContentType::HTML;
    }
    else {
      $this->headers['Content-Type'] = ContentType::JSON;
    }
  }

  private function sendCode()
  {
    http_response_code($this->status);
  }

  private function sendHeaders()
  {
    foreach ($this->headers as $key => $value) {
      header(strtolower($key) . ':' . $value);
    }
  }

  private function sendCookies()
  {
    foreach ($this->cookies as $key => $value) {
      call_user_func('setcookie', $key, ...$value);
    }
  }

  public function __invoke()
  {
    return $this->__toString();
  }

  public function __toString()
  {
    $this->fixContentType();
    $this->sendCode();
    $this->sendHeaders();
    $this->sendCookies();

    try {
      if (is_string($this->content))
        return $this->content;
      return match ($this->headers['Content-Type']) {
        'application/json' => json_encode($this->content),
        'application/xml' => Parser::arrayToXml($this->content),
        default => throw new InternalServerError(
          "Response: conversion for content-type '"
          . $this->headers['Content-Type']
          . "' unsupported",
        )
      };
    }
    catch (\Throwable $th) {
      throw new InternalServerError(
        "Response: conversion for content-type '"
        . $this->headers['Content-Type']
        . "' failed",
        $th
      );
    }
  }
}