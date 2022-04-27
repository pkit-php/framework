<?php

namespace Pkit\Http;

class Response
{
  private
    $httpCode = 200,
    $contentType = 'text/html',
    $headers = [],
    $modified = false;

  private function setModified()
  {
    $this->modified = true;
  }

  public function getModified()
  {
    return $this->modified;
  }

  public function addHeader(string $key, string $value): self
  {
    $this->setModified();
    $this->headers[$key] = $value;
    return $this;
  }

  public function setContentType(string $contentType): self
  {
    $this->setModified();
    $this->contentType = $contentType;
    $this->addHeader('Content-Type', $contentType);
    return $this;
  }

  public function setHttpCode(int $httpCode): self
  {
    $this->setModified();
    $this->httpCode = $httpCode;
    return $this;
  }

  public function status(int $httpCode): self
  {
    return $this->setHttpCode($httpCode);
  }

  public function getContentType()
  {
    return $this->contentType;
  }

  public function getHttpCode()
  {
    return $this->httpCode;
  }

  public function getStatus()
  {
    return $this->getHttpCode();
  }

  public function getHeaders()
  {
    return $this->headers;
  }

  private function sendCode()
  {
    if ($this->httpCode < 600 && $this->httpCode >= 100) {
      http_response_code($this->httpCode);
    } else {
      http_response_code(500);
    }
  }

  public function sendStatus($status = 200)
  {
    $this
      ->onlyCode()
      ->setHttpCode($status)
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

  public function json(): self
  {
    $this->setContentType('application/json');
    return $this;
  }

  public function onlyCode(): self
  {
    return $this->setContentType('');
  }

  public function serviceUnavailable(): self
  {
    return $this->setHttpCode(503);
  }

  public function notImplemented(): self
  {
    return $this->setHttpCode(501);
  }

  public function error(): self
  {
    return $this->setHttpCode(500);
  }

  public function fieldsTooLarges(): self
  {
    return $this->setHttpCode(431);
  }

  public function tooManyRequests(): self
  {
    return $this->setHttpCode(429);
  }

  public function unprocessableEntity(): self
  {
    return $this->setHttpCode(422);
  }

  public function unsupportedMediaType(): self
  {
    return $this->setHttpCode(415);
  }

  public function conflict()
  {
    return $this->setHttpCode(409);
  }

  public function notAcceptable()
  {
    return $this->setHttpCode(406);
  }

  public function methodNotAllowed(): self
  {
    return $this->setHttpCode(405);
  }

  public function notFound(): self
  {
    return $this->setHttpCode(404);
  }

  public function forbidden()
  {
    return $this->setHttpCode(403);
  }

  public function unauthorized()
  {
    return $this->setHttpCode(401);
  }

  public function badRequest()
  {
    return $this->setHttpCode(400);
  }

  public function permanentRedirect()
  {
    return $this->setHttpCode(308);
  }

  public function temporaryRedirect()
  {
    return $this->setHttpCode(307);
  }

  public function notModified()
  {
    return $this->setHttpCode(304);
  }

  public function seeOther()
  {
    return $this->setHttpCode(303);
  }

  public function found()
  {
    return $this->setHttpCode(302);
  }

  public function movedPermanently()
  {
    return $this->setHttpCode(301);
  }

  public function multipleChoice()
  {
    return $this->setHttpCode(300);
  }

  public function partialContent()
  {
    return $this->setHttpCode(206);
  }

  public function resetContent()
  {
    return $this->setHttpCode(205);
  }

  public function noContent()
  {
    return $this->setHttpCode(204);
  }

  public function nonAuthoritativeInformation()
  {
    return $this->setHttpCode(203);
  }

  public function accepted()
  {
    return $this->setHttpCode(201);
  }

  public function created()
  {
    return $this->setHttpCode(201);
  }

  public function ok(): self
  {
    return $this->setHttpCode(200);
  }
}
