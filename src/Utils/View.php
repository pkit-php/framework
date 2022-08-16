<?php

namespace Pkit\Utils;

use Phutilities\Parse;
use Phutilities\Text;

class View
{
  private static ?string
    $path = null, $slotPath = null;

  public static function config(string $path)
  {
    Self::$path = $path;
  }

  public static function getPath()
  {
    if (!self::$path) {
      self::$path = Env::getEnvOrValue("VIEW_PATH", $_SERVER["DOCUMENT_ROOT"] . "/view");
    }
    return self::$path;
  }

  public static function slot(mixed $args)
  {
    return self::render(self::$slotPath, $args);
  }

  private static function getBasePath(string $file)
  {
    if (substr($file, 0, 5) == 'pkit/') {
      return __DIR__ . '/../view/';
    } else {
      return Self::getPath() . '/';
    }
  }

  private static function getPathFile(string $file)
  {
    $file = Text::removeFromEnd($file, '.phtml') . '.phtml';
    $path = self::getBasePath($file);
    $file = Text::removeFromStart($file, 'pkit/');
    return $path . $file;
  }

  public static function render(string $file, mixed $args = null)
  {
    $_ARGS = $args;

    $path = self::getPathFile($file);
    if (!file_exists($path)) {
      throw new \Exception("VIEW: view '$path' not exists", 500);
    }
    ob_start();
    include $path;
    $content = ob_get_contents();

    ob_clean();
    ob_end_flush();

    return $content;
  }

  public static function layout(string $file, mixed $args = null)
  {
    $_ARGS = Parse::anyToArray($args);

    $path = self::getPathFile($file);
    if (!file_exists($path)) {
      throw new \Exception("VIEW: view '$path' not exists", 500);
    }
    $layout = self::getLayoutPath($file);

    ob_start();
    if (strlen($layout)) {
      self::$slotPath = $file;
      include $layout;
    } else {
      include $path;
    }
    self::$slotPath = '';
    $content = ob_get_contents();

    ob_clean();
    ob_end_flush();

    return $content;
  }

  public static function getLayoutPath(string $file)
  {
    $arrayPath = explode("/", $file);
    $index = count($arrayPath) - 1;
    unset($arrayPath[$index]);
    $path = self::getBasePath($file);
    $layout = '';
    $subpath = '';
    if (file_exists($path . $subpath . "/__layout.phtml")) {
      $layout = $path . "/__layout.phtml";
    }
    foreach ($arrayPath as $value) {
      $subpath .= "/$value";
      if (!strlen($layout) && file_exists($path . $subpath . "/__layout.phtml")) {
        $layout = $path . $subpath . "/__layout.phtml";
      } else if (file_exists($path . $subpath . "/__layout.reset.phtml")) {
        $layout = $path . $subpath . "/__layout.reset.phtml";
      }
    }
    return $layout;
  }
}
