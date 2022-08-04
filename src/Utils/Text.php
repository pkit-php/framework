<?php

namespace Pkit\Utils;

class Text
{
  static function removeFromEnd(string $haystack, string $needle): string
  {
    $length = strlen($needle);
    if($length > strlen($haystack)){
      return $haystack;
    }
    if (substr($haystack, -$length) === $needle) {
      $haystack = substr($haystack, 0, -$length);
    }
    return $haystack;
  }

  static function removeFromStart(string $haystack, string $needle): string
  {
    $length = strlen($needle);
    if($length > strlen($haystack)){
      return $haystack;
    }
    if (substr($haystack, 0, $length) === $needle) {
      $haystack = substr($haystack, $length);
    }
    return $haystack;
  }
}
