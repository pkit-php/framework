<?php

namespace Pkit\Http;

class Fetch
{
  private $curl;
  public readonly string $body;
  public readonly int $status;
  public readonly array $headers;

  public function __construct(string $url, string $method, $postFields = null, $headers = [])
  {
    $this->curl = curl_init($url);
    if ($this->curl) {
      $this->setOpts($method, $postFields, $headers);
      $this->exec();
      $error = curl_error($this->curl);
      if (strlen($error)) {
        throw new \Exception($error, 500);
      }
    }
  }

  private function exec()
  {
    $response = curl_exec($this->curl);
    $header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
    $this->setHeaders(substr($response, 0, $header_size));
    $this->body = substr($response, $header_size);
    curl_close($this->curl);
  }

  private function setHeaders(string $headers)
  {
    $explode = explode("\r\n", $headers);
    $result = $explode[0];

    $this->status = explode(" ", $result)[1];

    unset($explode[0]);
    $headers = [];
    foreach ($explode as $value) {
      $header = explode(":", $value);
      if ($header[1]) {
        $headers[$header[0]] = trim($header[1]);
      }
    }
    $this->headers = $headers;
  }

  private function setOpts(string $method, $postFields = null, $headers = [])
  {
    curl_setopt_array($this->curl, [
      CURLOPT_CUSTOMREQUEST => strtoupper($method),
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $postFields,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => true,
    ]);
  }

  public static function get(string $url, $postFields = null, $headers = [])
  {
    return new Self($url, 'GET', $postFields, $headers);
  }

  public static function post(string $url, $postFields = null, $headers = [])
  {
    return new Self($url, 'POST', $postFields, $headers);
  }

  public static function put(string $url, $postFields = null, $headers = [])
  {
    return new Self($url, 'PUT', $postFields, $headers);
  }

  public static function patch(string $url, $postFields = null, $headers = [])
  {
    return new Self($url, 'PATCH', $postFields, $headers);
  }

  public static function delete(string $url, $postFields = null, $headers = [])
  {
    return new Self($url, 'DELETE', $postFields, $headers);
  }

  public static function options(string $url, $postFields = null, $headers = [])
  {
    return new Self($url, 'OPTIONS', $postFields, $headers);
  }

  public static function trace(string $url, $postFields = null, $headers = [])
  {
    return new Self($url, 'TRACE', $postFields, $headers);
  }

  public static function head(string $url, $postFields = null, $headers = [])
  {
    return new Self($url, 'HEAD', $postFields, $headers);
  }
}
