<?php

namespace Pkit\Http;

class Response
{
  public array $headers;
  private ?int $status = null;
  private ?string $contentType = null;

  public function __construct()
  {
    $this->headers = [];
  }

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

  public function status(int $statusCode = 0): self | string
  {
    if ($statusCode) {
      $this->status = $statusCode;
      return $this;
    } else {
      return $this->status ?? 200;
    }
  }

  private function sendCode()
  {
    if ($this->status < 600 && $this->status >= 100) {
      http_response_code($this->status);
    } else {
      http_response_code(500);
    }
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

  public function send($content = '')
  {
    $this->setStatus();
    $this->setContentType();
    $this->sendCode();
    $this->sendHeaders();

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

  public function onlyCode(): self
  {
    return $this->setContentType(ContentType::NONE);
  }
}
