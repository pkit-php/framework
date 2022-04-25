<?php

namespace Pkit\Utils;

class View
{
  private static string $path;

  public static function init(string $path)
  {
    Self::$path = $path;
  }

  public static function render(string $file, $args = null)
  {
    if (ARGS) {
      $lastArgs = ARGS;
    };
    if ($args) define('ARGS', $args, false);
    echo $args;

    $path = Self::$path . '/' . explode('.php', ltrim($file, '/'))[0] . '.php';
    include $path;

    if ($lastArgs) define('ARGS', $lastArgs, false);
  }

  public static function layout(string $file, $args = null)
  {
    if (ARGS) $lastArgs = ARGS;
    if ($args) define('ARGS', $args, false);

    $path = Self::$path . '/' . rtrim(ltrim($file, '/'), '.php') . '.php';
    $layout = Self::$path . "/__layout.php";
    if (file_exists($layout)) {
      define('SLOT', $path);
      include $layout;
    } else {
      include $path;
    }

    if ($lastArgs) define('ARGS', $lastArgs, false);
  }
}
