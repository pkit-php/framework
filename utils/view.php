<?php

namespace Pkit\Utils;

class View
{
  private static string
    $path, $slotPath;
  private static $args, $argsBuffer;

  public static function init(string $path)
  {
    Self::$path = $path;
  }

  public static function getArgs()
  {
    return self::$args;
  }

  private static function allocArgs($args)
  {
    self::$argsBuffer = self::$args;
    self::$args = $args;
  }

  private static function reAllocArgs()
  {
    self::$args = self::$argsBuffer;
  }

  public static function slot()
  {
    include self::$slotPath;
  }

  public static function render(string $file, $args = null)
  {
    self::allocArgs($args);
    $file = Text::removeFromEnd($file, '.php');
    $path = Self::$path . '/' . $file . '.php';
    include $path;

    self::reAllocArgs();
  }

  public static function layout(string $file, $args = null)
  {
    self::allocArgs($args);
    $file = Text::removeFromEnd($file, '.php');
    $path = Self::$path . '/' . $file . '.php';
    $layout = Self::$path . "/__layout.php";
    if (file_exists($layout)) {
      self::$slotPath = $path;
      include $layout;
    } else {
      include $path;
    }

    self::reAllocArgs();
  }
}
