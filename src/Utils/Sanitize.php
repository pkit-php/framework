<?php

namespace Pkit\Utils;

class Sanitize
{
  static function sanitizeClass(string $class)
  {
    return @end(explode("\\", $class));
  }

  static function sanitizeURI(string $uri)
  {
    return urldecode(parse_url($uri, PHP_URL_PATH));
  }

  static function sanitizeProperties(object $object)
  {
    $array = (array)$object;
    foreach ($array as $key => $value) {
      unset($array[$key]);
      if (!preg_match('/\\\/', $key)) {
        $key = str_replace("\0*\0", "", $key);
        if (substr($key, 0, 1) != "_") {
          $array[$key] = $value;
        }
      }
    }
    return $array;
  }
}
