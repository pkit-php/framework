<?php

namespace Pkit\_;

class Routes
{
    private static
        $patternGeral = '/{(.*?)}|\[(.*?)\]|\((.*?)\)/',
        $patternSymbols = '/[{}\[\]\(\)]/',
        $patternVariable = '/\((.*?)\)/',
        $patternPath = '/\[(.*?)\]/',
        $patternRest = '/{(.*?)}/';

    public static function mathRoutes(array $routes, string $uri)
    {
        $includeFile = '';
        $params = [];
        foreach ($routes as $route => $file) {
            $result = self::mathRouteAndParams($route, $uri);
            if (is_array($result)) {
                $params = $result;
                $includeFile = $file;
                break;
            }
        }
        return [$includeFile, $params ?? []];
    }

    public static function mathRouteAndParams($route, $uri)
    {
        $variables = [];
        $route = str_replace('.', '\.', $route);
        $route = '/^' . str_replace('/', '\/', $route) . '$/';
        if (preg_match_all(self::$patternGeral, $route, $matches)) {
            $route = preg_replace(self::$patternVariable, '(\d*\w*)', $route);
            $route = preg_replace(self::$patternPath, '([^\/\s]*)', $route);
            $route = preg_replace(self::$patternRest, '([^\s]*)', $route);
            $variables = $matches[0];
            $patternSymbols = self::$patternSymbols;
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

            return $params ?? [];
        };

        return false;
    }
}
