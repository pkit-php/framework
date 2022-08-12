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
      try {
        $value = $prop->getValue($object);
      } catch (\Throwable $th) {
        $value = null;
      }
      $array[$prop->getName()] = $value;
    }
    return $array;
  }

  public static function url(string $url): string
  {
    return filter_var($url, FILTER_SANITIZE_URL);
  }

  public static function int(string $int): string
  {
    return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
  }

  public static function email(string $email): string
  {
    return filter_var($email, FILTER_SANITIZE_EMAIL);
  }

  public static function float(string $float): string
  {
    return filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT);
  }

  public static function encoded(string $encoded): string
  {
    return filter_var($encoded, FILTER_SANITIZE_ENCODED);
  }

  public static function slashes(string $slashes): string
  {
    return filter_var($slashes, FILTER_SANITIZE_ADD_SLASHES);
  }

  public static function special_chars(string $special_chars): string
  {
    return filter_var($special_chars, FILTER_SANITIZE_SPECIAL_CHARS);
  }

  public static function full_especial_chars(string $full_especial_chars): string
  {
    return filter_var($full_especial_chars, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }
}
