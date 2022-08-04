<?php

namespace Pkit\Utils;

use Closure;

class FS
{
  static public function someFile(string $path, Closure $map, $recursive = false): string | false
  {
    $path = rtrim($path);
    $directory = dir($path);
    while ($file = $directory->read()) {
      if (@dir("$path/$file")) {
        if ($recursive && $file !== '.' && $file !== '..') {
          if ($result = self::someFile("$path/$file", $map , $recursive)){
            return $result;
          };
        }
      } else {
        if ($map($path . "/" . $file) == true) {
          return $path . "/" . $file;
        };
      }
    }
    $directory->close();
    return false;
  }
}
