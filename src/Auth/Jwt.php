<?php

namespace Pkit\Auth;

use DateTime;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Auth\Jwt\JwtEnv;

class Jwt extends JwtEnv
{
  public static $supported_algs = [
    'HS256' => ['hash_hmac', 'SHA256'],
    'HS384' => ['hash_hmac', 'SHA384'],
    'HS512' => ['hash_hmac', 'SHA512'],
  ];

  private static function base64url_encode(mixed $data)
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  private static function base64url_decode(mixed $data)
  {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
  }

  public static function getPayload(string $token): mixed
  {
    $part = explode(".", $token);
    $payload = self::base64url_decode($part[1]);
    return json_decode($payload);
  }

  public static function setBearer(Response $response, string $token): Response
  {
    return $response->header("authorization", "Bearer " . $token);
  }

  public static function getBearer(Request $request): string|false
  {
    $authorization = $request->headers["authorization"];
    $prefix_bearer = "Bearer ";
    if (is_null($authorization))
      return false;
    if (substr($authorization, 0, strlen($prefix_bearer)) !== $prefix_bearer)
      return false;
    return substr($authorization, strlen($prefix_bearer));
  }

  private static function signature(string $header, string $payload): string|false
  {
    $json_header = json_decode(self::base64url_decode($header));
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
    return self::base64url_encode($signature);
  }

  public static function tokenize(array $payload): string
  {
    $header = [
      'alg' => self::getAlg(),
      'typ' => 'JWT'
    ];

    if (self::getExpire()) {
      $payload['_created'] = (new DateTime())->format('Y-m-d H:i:s');
    }

    $header = json_encode($header);
    $header = self::base64url_encode($header);

    $payload = json_encode($payload);
    $payload = self::base64url_encode($payload);

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