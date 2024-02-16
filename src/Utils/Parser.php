<?php

namespace Pkit\Utils;

class Parser
{

  public static function headerToArray(string $header, bool $recursive = true)
  {
    $return = [];
    foreach (explode(";", $header) as $index => $line) {
      foreach (explode(",", $line) as $parse) {
        parse_str($parse, $result);
        foreach ($result as $key => $value) {
          if ($recursive) {
            if ($value == "") {
              $return[$index][] = $key;
              continue;
            }
            $return[$index][$key] = $value;
          } else {
            if ($value == "") {
              $return[] = $key;
              continue;
            }
            $return[$key] = $value;
          }
        }
      }
    }
    return $return;
  }

  public static function xmlToArray(string $xml): array
  {
    $_xml = new \SimpleXMLElement($xml);

    $array = json_decode(json_encode($_xml), 1);
    $root = $_xml->getName();
    if ($root !== "root") {
      $array = ["@root" => $root, ...$array];
    }
    return $array;
  }

  public static function arrayToXml(array $array): string
  {

    $rootElement = @$array["@root"] ?? "root";
    unset($array["@root"]);

    if (key_exists("@child", $array))
      return (new \SimpleXMLElement("<$rootElement>" . $array["@child"] . "</$rootElement>"))->asXML();

    $xml = new \SimpleXMLElement("<$rootElement/>");
    self::handleArrayToXml($array, $xml);

    return $xml->asXML();
  }

  private static function handleArrayToXml(array $array, \SimpleXMLElement $xml, ?string $outerTag = null)
  {
    foreach ($array as $tag => $children) {
      if ($tag == "@attributes") {
        foreach ($children as $attribute => $valueAttribute) {
          $xml->addAttribute($attribute, $valueAttribute);
        }
        continue;
      }
      if (!is_array($children)) {
        $xml->addChild($tag, $children);
        continue;
      }
      self::handleArrayToXml($children, $outerTag ? $xml->addChild($outerTag): $xml, $tag);
    }
  }
}