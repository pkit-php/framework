<?php

namespace Pkit\Http;

use Pkit\Throwable\Error;
use Phutilities\Parse;
use Pkit\Utils\View;

class Response
{
  private array $headers = [];
  private array $cookies = [];
  private ?string $contentType = null;
  private int $status;
  private array | string | object $content;

  public function __construct(array | string | object $content, $status = 200)
  {
    $this->content = $content;
    $this->status = $status;
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
  ) {
    $this->cookies[$name] = [
      "value" => $value,
      "expires_or_options" => $expires_or_options,
      "path" => $path,
      "domain" => $domain,
      "secure" => $secure,
      "httponly" => $httponly
    ];
    return $this;
  }

  public function contentType(string $contentType)
  {
    $this->contentType = $contentType;
    return $this;
  }

  public function status(int $statusCode = 0): self | int
  {
    if (!Status::validate($statusCode))
      throw new Error(
        "Response: Status '$statusCode' is not valid",
        Status::INTERNAL_SERVER_ERROR
      );
    $this->status = $statusCode;
    return $this;
  }

  public static function render(string $file, int $status = 200,mixed $args = null, bool $layout = true)
  {
    if($layout)
      $render = View::layout($file, $args);
    else
      $render = View::render($file, $args);
    
    return (new Response($render, $status))
      ->contentType(ContentType::HTML);
  }

  public static function json(array|string $content, int $status)
  {
    return (new Response($content, $status))
      ->contentType(ContentType::JSON);
  }

  public static function xml(array|string $content, int $status)
  {
    return (new Response($content, $status))
      ->contentType(ContentType::XML);
  }

  private function fixContentType()
  {
    if (is_null($this->contentType)) {
    if (is_string($this->content)) {
        $this->contentType = ContentType::HTML;
    } else {
        $this->contentType = ContentType::JSON;
      }
    }
  }

  private function sendCode()
  {
    http_response_code($this->status);
  }

  private function sendHeaders()
  {
    foreach ($this->headers as $key => $value) {
      header($key . ':' . $value);
    }
  }

  private function sendCookies()
  {
    foreach ($this->cookies as $key => $value) {
      call_user_func('setcookie', $key, ...$value);
    }
  }

  public function __toString()
  {
    $this->fixContentType();
    $this->sendCode();
    $this->sendHeaders();
    $this->sendCookies();

    try {
      return match ($this->contentType) {
        'text/html' => $this->content,
        'application/json' => is_array($this->content)
          ? json_encode(
            $this->content,
          )
          : $this->content,
        'application/xml' => is_array($this->content)
          ? Parse::arrayToXml($this->content)
          : $this->content,
        default => $this->content
      };
    } catch (\Throwable $th) {
      throw new Error(
        "Response: convertion for content-type'"
          . $this->contentType
          . "'",
        500,
        $th
      );
    }
  }
}
