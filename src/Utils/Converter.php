<?php

namespace Pkit\Utils;

class Converter
{

  public static function anyToArray(mixed $any)
  {
    if (!is_array($any)) {
      $any = [$any];
    }
    return $any;
  }

  public static function objectToArray(object $object)
  {
    foreach ($object as $key => $value) {
      $array[$key] = $value;
    }
    return $array;
  }

  public static function xmlToArray(string $xml)
  {
    $string = simplexml_load_string($xml);
    return json_decode(json_encode($string), 1);
  }
}
