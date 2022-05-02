<?php

namespace Pkit\Utils;

class Sanitize
{
  static function sanitizeClass($class)
  {
    return @end(explode("\\", $class));
  }

  static function sanitizeURI(string $uri)
  {
    $uri_base = explode('?', $uri)[0];
    $pure_uri = explode('#', $uri_base)[0];

    $xUri = $pure_uri ?? '/';
    $yUri = rtrim($xUri, '/');
    if ($yUri == '') {
      return '/';
    }

    return $yUri;
  }

  static function sanitizeProperties($array)
  {
    foreach ($array as $key => $value) {
      unset($array[$key]);
      if (!preg_match('/\\\/', $key)) {
        $protected = chr(0) . "*" . chr(0);
        $key = str_replace($protected, "", $key);
        $array[$key] = $value;
      }
    }
    return $array;
  }
}
