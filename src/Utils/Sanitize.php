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
    $uri = urldecode($uri);
    $uri = explode('?', $uri)[0];
    $uri = explode('#', $uri)[0];

    $uri = $uri ?? '/';
    return $uri == '/' ? $uri : rtrim($uri, '/');
  }

  static function sanitizeProperties($array)
  {
    foreach ($array as $key => $value) {
      unset($array[$key]);
      if (!preg_match('/\\\/', $key)) {
        $protected = chr(0) . "*" . chr(0);
        $key = str_replace($protected, "", $key);
        if (substr($key, 0, 1) != "_") {
          $array[$key] = $value;
        }
      }
    }
    return $array;
  }
}
