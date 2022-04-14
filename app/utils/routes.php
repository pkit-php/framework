<?php

function getRoutes($path, $subpath = "/")
{
    $routes = [];
    $directory = dir($path . $subpath);
    while ($file = $directory->read()) {
        if (dir($path . $subpath . $file)) {
            if ($file !== '.' && $file !== '..') {
                $routes = array_merge($routes, getRoutes($path, $subpath . $file . "/"));
            }
        } else {
            $route = $subpath . ($file == 'index.php' ? '' : explode('.php', $file)[0] . '/');
            $routes[$route] = $path . $subpath . $file;
        }
    }
    $directory->close();
    return $routes;
}
