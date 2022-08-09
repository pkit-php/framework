<?php

namespace Pkit\Http;

class ContentType
{
  public static function validate(string $content)
  {
    $constantNames = (new \ReflectionClass(self::class))
      ->getConstants();
    return in_array($content, $constantNames);
  }
  const NONE = "*";
  const JSON = "application/json";
  const HTML = "text/html";
  const XML = "application/xml";
}
