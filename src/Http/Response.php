<?php

namespace Pkit\Http;

class Response
{
  public array $headers = [];
  private array $cookies = [];
  private ?int $status = null;
  private ?string $contentType = null;

  public function setStatus($status = 200)
  {
    if (!$this->status) {
      $this->status($status);
    }
    return $this;
  }

  public function setContentType($contentType = 'text/html')
  {
    if (!$this->contentType) {
      $this->contentType($contentType);
    }
    return $this;
  }


  public function contentType(?string $contentType = null): self | string
  {
    if ($contentType) {
      $this->contentType = $contentType;
      return $this;
    } else {
      return $this->contentType ?? "text/html";
    }
  }

  public function status(int $statusCode = 0): self | int
  {
    if ($statusCode) {
      $this->status = $statusCode;
      return $this;
    } else {
      return $this->status ?? 200;
    }
  }

  public function setCookie(
    string $name,
    $value = "",
    $expires_or_options = 0,
    $path = "",
    $domain = "",
    $secure = false,
    $httponly = false
  ) {
    $this->cookies[$name] = [
      "value" => $value,
      "expires_or_options" => $expires_or_options,
      "path" => $path,
      "domain" => $domain,
      "secure" => $secure,
      "httponly" => $httponly
    ];
  }

  private function sendCode()
  {
    http_response_code($this->status);
  }

  public function sendStatus($status = 200)
  {
    $this
      ->onlyCode()
      ->status($status)
      ->send();
  }

  private function sendHeaders()
  {
    $this->headers['Content-Type'] = $this->contentType;

    foreach ($this->headers as $key => $value) {
      if ($value) {
        header($key . ':' . $value);
      } else {
        header_remove($value);
      }
    }
  }

  private function sendCookies()
  {
    foreach ($this->cookies as $key => $value) {
      call_user_func('setcookie', $key, ...$value);
    }
  }

  public function send($content = '')
  {
    $this->setStatus();
    $this->setContentType();
    $this->sendCode();
    $this->sendHeaders();
    $this->sendCookies();

    switch ($this->contentType) {
      case 'text/html':
        echo $content;
        break;
      case 'application/json':
        echo json_encode(
          $content,
          JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        break;
      default:
        echo $content;
        break;
    }

    exit;
  }

  public function render($content = '', $status = 200)
  {
    $this->setStatus($status)->contentType(ContentType::HTML)->send($content);
  }

  public function onlyCode(): self
  {
    return $this->setContentType(ContentType::NONE);
  }
}
