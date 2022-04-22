<?php

namespace Pkit\Utils;

class Converter
{
  public static function objectToArray($object)
  {
    foreach ($object as $key => $value) {
      $array[$key] = $value;
    }
    return $array;
  }
}
