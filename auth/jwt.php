<?php

namespace Pkit\Auth;

use DateTime;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Utils\Base64url;
use Pkit\Utils\Text;

class Jwt
{
  private static $key, $expire = 0;

  public static function init(string $key, $expire = 0)
  {
    self::$key = $key;
    self::$expire = $expire;
  }

  private static function signature(string $header, string $payload)
  {
    $signature = hash_hmac('sha256', "$header.$payload", self::$key, true);
    return Base64url::encode($signature);
  }

  public static function tokenize(array $payload)
  {
    $header = [
      'alg' => 'HS256',
      'typ' => 'JWT'
    ];

    if (self::$expire) {
      $payload['_created'] = (new DateTime())->format('Y-m-d H:i:s');
    }

    $header = json_encode($header);
    $header = Base64url::encode($header);

    $payload = json_encode($payload);
    $payload = Base64url::encode($payload);

    $signature = self::signature($header, $payload);

    return "$header.$payload.$signature";
  }

  public static function getPayload(string $token)
  {
    $part = explode(".", $token);
    $payload = Base64url::decode($part[1]);
    return json_decode($payload);
  }

  public static function setBearer(Response $response, string $token)
  {
    $response->header["authorization"] = "Bearer " . $token;
  }

  public static function getBearer(Request $request)
  {
    $authorization = $request->headers["authorization"] ?? "";
    return Text::removeFromStart($authorization, "Bearer ");
  }

  public static function getExpire()
  {
    return self::$expire;
  }

  public static function validate(string $token)
  {
    $part = explode(".", $token);
    $header = $part[0];
    $payload = $part[1];
    $signature = $part[2];

    $valid = self::signature($header, $payload);

    return $signature == $valid;
  }
}
