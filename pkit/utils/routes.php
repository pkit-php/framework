<?php

namespace Pkit\Utils;

class Routes
{
    public static function getRoutes(string $path, $subpath = "/")
    {
        $routes = [];
        $routesDirs = [];
        $directory = dir($path . $subpath);
        while ($file = $directory->read()) {
            if (@dir($path . $subpath . $file)) {
                if ($file !== '.' && $file !== '..') {
                    $routesDirs = array_merge($routesDirs, Routes::getRoutes($path, $subpath . $file . "/"));
                }
            } else {
                $route = $subpath . ($file == 'index.php' ? '' : rtrim($file, '.php') . '/');
                $routes[$route] = $path . $subpath . $file;
            }
        }
        $directory->close();
        return array_merge($routes, $routesDirs);
    }

    public static function mathRoute(array $routes, string $uri)
    {
        $includeFile = '';
        $params = [];
        $variables = [];

        $patternGeral = '/{(.*?)}|\[(.*?)\]|\((.*?)\)/';
        $patternSymbols = '/[{}\[\]\(\)]/';
        $patternVariable = '/\((.*?)\)/';
        $patternPath = '/\[(.*?)\]/';
        $patternRest = '/{(.*?)}/';

        foreach ($routes as $route => $file) {
            $variables = [];
            $route = str_replace('.', '\.', $route);
            $route = '/^' . str_replace('/', '\/', $route) . '$/';
            if (preg_match_all($patternGeral, $route, $matches)) {
                $route = preg_replace($patternVariable, '(\d*\w*)', $route);
                $route = preg_replace($patternPath, '([^\/\s]*)', $route);
                $route = preg_replace($patternRest, '([^\s]*)', $route);
                $variables = $matches[0];
                $variables = array_map(function ($var) use ($patternSymbols) {
                    $var = preg_replace($patternSymbols, '', $var);
                    return $var;
                }, $variables);
            }

            if (preg_match($route, $uri)) {

                if (preg_match($route, $uri, $matches)) {
                    unset($matches[0]);
                    $params = array_combine($variables, $matches);
                };

                $includeFile = $file;
                break;
            };
        }

        return [$includeFile, $params];
    }
}
