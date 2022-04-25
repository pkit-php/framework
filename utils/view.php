<?php

namespace Pkit\Utils;

class View
{
  private static string $path;

  public static function init(string $path)
  {
    Self::$path = $path;
  }

  public static function render(string $file, $args)
  {
    define('ARGS', $args);
    $path = self::$path . '/' . rtrim(ltrim($file, '/'), '.php') . '.php';
    include $path;
  }
}
