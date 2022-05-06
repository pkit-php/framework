<?php

namespace Pkit\Private;

use Pkit\Utils\Text;

class Map
{
  static function mapPhpFiles(string $path, $subpath = "/")
  {
    $routes = [];
    $routesDirs = [];
    $directory = dir($path . $subpath);
    while ($file = $directory->read()) {
      if (@dir($path . $subpath . $file)) {
        if ($file !== '.' && $file !== '..') {
          $routesDirs = array_merge($routesDirs, self::mapPhpFiles($path, $subpath . $file . "/"));
        }
      } else {
        $route = $subpath . ($file == 'index.php' ? '' : Text::removeFromEnd($file, '.php'));
        $route = $route == "/" ? $route : rtrim($route, '/');

        $routes[$route] = $path . $subpath . $file;
      }
    }
    $directory->close();
    return array_merge($routes, $routesDirs);
  }
}
