<?php

namespace Pkit\Auth;

use DateTime;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Auth\Jwt\JwtEnv;
use Pkit\Throwable\Error;
use Phutilities\Base64url;
use Phutilities\Date;
use Phutilities\Text;

class Jwt extends JwtEnv
{
  public static $supported_algs = [
    'HS256' => ['hash_hmac', 'SHA256'],
    'HS384' => ['hash_hmac', 'SHA384'],
    'HS512' => ['hash_hmac', 'SHA512'],
  ];

  public static function getPayload(string $token): string
  {
    $part = explode(".", $token);
    $payload = Base64url::decode($part[1]);
    return json_decode($payload);
  }

  public static function setBearer(Response $response, string $token): Response
  {
    return $response->header("authorization", "Bearer " . $token);
  }

  public static function getBearer(Request $request): string | false
  {
    $authorization = $request->headers["authorization"];
    if (is_null($authorization))
      return false;
    return Text::removeFromStart($authorization, "Bearer ");
  }

  private static function signature(string $header, string $payload): string
  {
    $alg = self::$supported_algs[self::getAlg()];
    if (is_null($alg))
      throw new Error("Jwt: algorithm '" . self::getAlg() . "' not supported", 500);

    $signature =  call_user_func_array($alg[0], [
      strtolower($alg[1]),
      "$header.$payload",
      self::getKey(),
      true
    ]);
    return Base64url::encode($signature);
  }

  public static function tokenize(array $payload): string
  {
    $header = [
      'alg' => self::getAlg(),
      'typ' => 'JWT'
    ];

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

  public static function validate(string $token): bool
  {
    $part = explode(".", $token);
    $header = $part[0];
    $payload = $part[1];
    $signature = $part[2];

    if (is_null($signature))
      return false;

    $valid = self::signature($header, $payload);

    return $signature == $valid;
  }
}
