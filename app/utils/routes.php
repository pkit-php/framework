<?php

function getRoutes($path, $subpath = "/")
{
    $routes = [];
    $routesDirs = [];
    $directory = dir($path . $subpath);
    while ($file = $directory->read()) {
        if (@dir($path . $subpath . $file)) {
            if ($file !== '.' && $file !== '..') {
                $routesDirs = array_merge($routesDirs, getRoutes($path, $subpath . $file . "/"));
            }
        } else {
            $route = $subpath . ($file == 'index.php' ? '' : explode('.php', $file)[0] . '/');
            $routes[$route] = $path . $subpath . $file;
        }
    }
    $directory->close();
    return array_merge($routes, $routesDirs);
}
