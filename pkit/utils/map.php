<?php

namespace Pkit\Utils;

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
          $routesDirs = array_merge($routesDirs, Map::mapPhpFiles($path, $subpath . $file . "/"));
        }
      } else {
        $route = $subpath . ($file == 'index.php' ? '' : rtrim($file, '.php') . '/');
        $routes[$route] = $path . $subpath . $file;
      }
    }
    $directory->close();
    return array_merge($routes, $routesDirs);
  }
}
