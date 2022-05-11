<?php

namespace Pkit\Utils;

class Base64url
{
  static function encode(mixed $data)
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  static function decode(mixed $data)
  {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
  }
}
