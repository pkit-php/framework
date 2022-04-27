<?php

namespace Pkit\Utils;

class Jwt
{
  private static $key;

  public static function init($key)
  {
    self::$key = $key;
  }

  private static function signature($header, $payload)
  {
    $signature = hash_hmac('sha256', "$header.$payload", self::$key, true);
    return Base64url::encode($signature);
  }

  public static function tokenize($payload)
  {
    $header = [
      'alg' => 'HS256',
      'typ' => 'JWT'
    ];

    $header = json_encode($header);
    $header = Base64url::encode($header);

    $payload = json_encode($payload);
    $payload = Base64url::encode($payload);

    $signature = self::signature($header, $payload);

    return "$header.$payload.$signature";
  }

  public static function getPayload($token)
  {
    $part = explode(".", $token);
    $payload = Base64url::decode($part[1]);
    return json_decode($payload);
  }

  public static function validate($token)
  {
    $part = explode(".", $token);
    $header = $part[0];
    $payload = $part[1];
    $signature = $part[2];

    $valid = self::signature($header, $payload);

    return $signature == $valid;
  }
}
