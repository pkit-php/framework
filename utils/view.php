<?php

namespace Pkit\Utils;

use Pkit\Http\ContentType;
use Pkit\Http\Response;

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
    return $args;
  }

  private static function reAllocArgs()
  {
    self::$args = self::$argsBuffer;
  }

  public static function slot()
  {
    include self::$slotPath;
  }

  private static function getPath(string $file)
  {
    $file = Text::removeFromEnd($file, '.php') . '.php';

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
    self::allocArgs($args);

    $path = self::getPath($file);
    include $path;

    self::reAllocArgs();

    if ($response) {
      View::sendHtml($response, $code);
    }
  }

  public static function layout(string $file, ?Response $response = null, $args = null, $code = 200)
  {
    self::allocArgs($args);

    $path = self::getPath($file);
    $layout = Self::$path . "/__layout.php";

    if (substr($file, 0, 5) != 'pkit/' && file_exists($layout)) {
      self::$slotPath = $path;
      include $layout;
    } else {
      include $path;
    }

    self::reAllocArgs();

    if ($response) {
      View::sendHtml($response, $code);
    }
  }
}
