<?php

namespace Pkit\Utils;

use Pkit\Http\ContentType;
use Pkit\Http\Response;

class View
{
  private static string
    $path, $slotPath;

  public static function init(string $path)
  {
    Self::$path = $path;
  }

  public static function slot($args)
  {
    $_ARGS = $args;
    include self::$slotPath;
  }

  private static function getPath(string $file)
  {
    $file = Text::removeFromEnd($file, '.phtml') . '.phtml';

    if (substr($file, 0, 5) == 'pkit/') {
      $file = Text::removeFromStart($file, 'pkit/');
      return __DIR__ . '/../view/' .  $file;
    } else {
      return Self::$path . '/' . $file;
    }
  }

  private static function sendHtml(Response $response, int $code)
  {
    $response->contentType(ContentType::HTML)->setStatus($code)->send();
  }

  public static function render(string $file, ?Response $response = null, $args = null, $code = 200)
  {
    $_ARGS = $args;

    $path = self::getPath($file);
    include $path;

    if ($response) {
      View::sendHtml($response, $code);
    }
  }

  public static function layout(string $file, ?Response $response = null, $args = null, $code = 200)
  {
    $_ARGS = $args;

    $path = self::getPath($file);
    $layout = self::getLayoutPath($file);

    if (substr($file, 0, 5) != 'pkit/' && strlen($layout)) {
      self::$slotPath = $path;
      include $layout;
    } else {
      include $path;
    }

    if ($response) {
      View::sendHtml($response, $code);
    }
  }

  public static function getLayoutPath($file)
  {
    $arrayPath = explode("/", $file);
    $index = count($arrayPath) - 1;
    unset($arrayPath[$index]);
    $layout = '';
    $path = '';
    if (file_exists(Self::$path . $path . "/__layout.phtml")) {
      $layout = Self::$path . "/__layout.phtml";
    }
    foreach ($arrayPath as $value) {
      $path .= "/$value";
      if (!strlen($layout) && file_exists(Self::$path . $path . "/__layout.phtml")) {
        $layout = Self::$path . $path . "/__layout.phtml";
      } else if (file_exists(Self::$path . $path . "/__layout.reset.phtml")) {
        $layout = Self::$path . $path . "/__layout.reset.phtml";
      }
    }
    return $layout;
  }
}
