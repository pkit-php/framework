<?php

namespace Pkit\Auth;

use DateTime;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Auth\Jwt\JwtEnv;
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

  public static function getPayload(string $token): mixed
  {
    $part = explode(".", $token);
    $payload = Base64url::decode($part[1]);
    return json_decode($payload);
  }

  public static function setBearer(Response $response, string $token): Response
  {
    return $response->header("authorization", "Bearer " . $token);
  }

  public static function getBearer(Request $request): string|false
  {
    $authorization = $request->headers["authorization"];
    if (is_null($authorization))
      return false;
    return Text::removeFromStart($authorization, "Bearer ");
  }

  private static function signature(string $header, string $payload): string|false
  {
    $json_header = json_decode(Base64url::decode($header));
    if (!is_object($json_header))
      return false;

    $alg = self::$supported_algs[$json_header->alg];
    if (is_null($alg))
      return false;

    $signature = call_user_func_array($alg[0], [
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

    if (!$valid)
      return false;

    return $signature == $valid;
  }
}