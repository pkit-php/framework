<?php

namespace Pkit\Private;

class Routes
{
    private static
        $patternVariable = '/\[\w+\]/',
        $patternRest = '/\[\.{3}\w+\]/',
        $patternGeral = '/\[(?:\.{3})?(\w+)\]/';

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
        if (preg_match_all(self::$patternGeral, $route, $matches)) {
            $variables = $matches[1];
            $regex = [
                self::$patternRest => "\1",
                self::$patternVariable => "\2",
            ];
            $route = preg_replace(
                array_keys($regex),
                array_values($regex),
                $route
            );
        }

        $route = preg_quote($route);
        $route = str_replace('/', '\/', $route);
        $route = strtr($route, [
            "\1" => '(.*)',
            "\2" => '([^\/]*)',
        ]);
        $route = '/^' . $route . '$/';

        if (preg_match($route, $uri, $matches)) {
            unset($matches[0]);
            $params = array_combine($variables ?? [], $matches);
            return $params;
        };

        return false;
    }
}
