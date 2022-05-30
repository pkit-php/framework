<?php

namespace Pkit\Utils;

use ReflectionClass;
use ReflectionProperty;

class Sanitize
{
  static function class(string $class)
  {
    return (new ReflectionClass($class))->getShortName();
  }

  static function uri(string $uri)
  {
    $uri = urldecode(parse_url($uri, PHP_URL_PATH));
    return $uri != "/" ? rtrim($uri, "/") : $uri;
  }

  static function objectProperties(object $object)
  {
    $reflect = new ReflectionClass($object);
    $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
    $array = [];
    foreach ($props as $prop) {
      $array[$prop->getName()] = $prop->getValue($object);
    }
    return $array;
  }
}
