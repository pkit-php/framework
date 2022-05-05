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
    return self::render(self::$slotPath, null, $args);
  }

  private static function getBasePath($file)
  {
    if (substr($file, 0, 5) == 'pkit/') {
      return __DIR__ . '/../view/';
    } else {
      return Self::$path . '/';
    }
  }

  private static function getPath(string $file)
  {
    $file = Text::removeFromEnd($file, '.phtml') . '.phtml';
    $path = self::getBasePath($file);
    $file = Text::removeFromStart($file, 'pkit/');
    return $path . $file;
  }

  private static function sendHtml(Response $response, int $code, string $content)
  {
    $response->contentType(ContentType::HTML)->setStatus($code)->send($content);
  }

  public static function render(string $file, ?Response $response = null, $args = null, $code = 200)
  {
    $_ARGS = $args;

    $path = self::getPath($file);
    if (!file_exists($path)) {
      throw new \Exception("VIEW: view '$file' not exists", 500);
    }
    include $path;

    if ($response) {
      View::sendHtml($response, $code);
    }
  }

  public static function layout(string $file, ?Response $response = null, $args = null, $code = 200)
  {
    $_ARGS = $args;

    $path = self::getPath($file);
    if (!file_exists($path)) {
      throw new \Exception("VIEW: view '$file' not exists", 500);
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

    if ($response) {
      View::sendHtml($response, $code, $content);
    }
    return $content;
  }

  public static function getLayoutPath($file)
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
