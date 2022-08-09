<?php

namespace Pkit\Utils;

use Pkit\Throwable\Error;

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

  public static function xmlToArray(string $xml): array
  {
    $_xml = new \SimpleXMLElement($xml);
      
    $array = json_decode(json_encode($_xml), 1);
    $root = $_xml->getName();
    if($root !== "root"){
      $array = ["@root" => $root,...$array];
    }
    return $array;
  }

  public static function arrayToXml(array $array): string
  {
    
    $rootElement = @$array["@root"] ?? "root";
    unset($array["@root"]);

    $xml = new \SimpleXMLElement("<$rootElement/>");
    self::handleArrayToXml($array, $xml);

    return $xml->asXML();
  }

  private static function handleArrayToXml(array $array, \SimpleXMLElement $xml)
  {
    foreach ($array as $tag => $children) {
      if ($tag == "@attributes") {
        foreach ($children as $attribute => $valueAttribute) {
          $xml->addAttribute($attribute, $valueAttribute);
        }
      } else if (is_array($children)) {
        self::handleArrayToXml($children, $xml->addChild($tag));
      } else {
        $xml->addChild($tag, $children);
      }
    }
  }
}
