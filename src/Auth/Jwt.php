<?php

namespace Pkit\Auth;

use DateTime;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Utils\Env;
use Pkit\Utils\Base64url;
use Pkit\Utils\Date;
use Pkit\Utils\Text;

class Jwt
{
  private static ?string $key = null;
  private static ?int $expire = null;
  private static string $alg = 'HS256';
  public static $supported_algs = [
    'HS256' => ['hash_hmac', 'SHA256'],
    'HS384' => ['hash_hmac', 'SHA384'],
    'HS512' => ['hash_hmac', 'SHA512'],
  ];

  public static function config(string $key, $expire = 0, $alg = 'HS256')
  {
    self::$key = $key;
    self::$expire = $expire;
    self::$alg = $alg;
  }

  private static function signature(string $header, string $payload)
  {
    $alg = self::$supported_algs[self::$alg];
    $signature =  call_user_func($alg[0], strtolower($alg[1]), "$header.$payload", self::getKey(), true);
    return Base64url::encode($signature);
  }

  public static function tokenize(array $payload)
  {
    $header = [
      'alg' => self::getAlg(),
      'typ' => 'JWT'
    ];

    var_dump(self::getExpire());
    if (self::getExpire()) {
      $payload['_created'] = Date::format(new DateTime());
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

  public static function getAlg()
  {
    if (is_null(self::$alg)) {
      self::$alg = Env::getEnvOrValue("JWT_ALG", 0);
    }
    return self::$alg;
  }

  public static function getExpire()
  {
    if (is_null(self::$expire)) {
      self::$expire = (int)Env::getEnvOrValue("JWT_EXPIRES", 0);
    }
    return self::$expire;
  }

  public static function getKey()
  {
    if (is_null(self::$key)) {
      self::$key = Env::getEnvOrValue("JWT_KEY", "");
    }
    return self::$key;
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
