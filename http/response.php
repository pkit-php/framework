<?php

namespace Pkit\Http;

class Response
{
  private readonly int $status;
  public string $contentType;
  public array $headers;
  public readonly bool $statusModified;

  public function __construct()
  {
    $this->headers = [];
  }

  public function setStatus($status = 200)
  {
    try {
      $_ = $this->status;
    } catch (\Throwable $th) {
      $this->status($status);
    }
    return $this;
  }
  public function setContentType($contentType = 'text/html')
  {
    try {
      $_ = $this->contentType;
    } catch (\Throwable $th) {
      $this->contentType($contentType);
    }
    return $this;
  }


  public function contentType(string $contentType): self
  {
    $this->contentType = $contentType;

    $this->headers['Content-Type'] = $this->contentType;
    return $this;
  }

  public function status(int $statusCode = 0): self
  {
    $this->statusModified = true;
    $this->status = $statusCode;
    return $this;
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
    foreach ($this->headers as $key => $value) {
      header($key . ':' . $value);
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
