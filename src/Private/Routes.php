<?php

namespace Pkit\Private;

class Routes
{
    private static
        $patternGeral = '/\[(.*?)\]|\[\.\.\.(.*?)\]/',
        $patternVariable = '/\[(.*?)\]/',
        $patternRest = '/\[\\\\.\\\\.\\\\.(.*?)\]/';

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

    public static function mathRouteAndParams(string $route, string $uri)
    {
        $variables = [];
        $route = str_replace('\\', '\\\\', $route);
        $route = str_replace('*', '\*', $route);
        $route = str_replace('(', '\(', $route);
        $route = str_replace(')', '\)', $route);
        $route = str_replace('{', '\{', $route);
        $route = str_replace('}', '\}', $route);
        $route = str_replace('.', '\.', $route);
        $route = '/^' . str_replace('/', '\/', $route) . '$/';
        if (preg_match_all(self::$patternGeral, $route, $matches)) {
            $route = preg_replace(self::$patternRest, '(.*)', $route);
            $route = preg_replace(self::$patternVariable, '([^\/]*)', $route);
            $variables = $matches[0];
            $variables = array_map(function ($var) {
                $var = trim($var, '\[.]');
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
