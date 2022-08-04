<?php

namespace Pkit\Http\Router;

class Routes
{
    private static
        $patternVariable = '/\[\w+\]/',
        $patternRest = '/\[\.{3}\w+\]/',
        $patternGeral = '/\[(?:\.{3})?(\w+)\]/';

    public static function matchRouteAndParams(string $route, string $uri): array | false
    {
        $variables = [];
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
            return array_combine(@$variables ?? [], $matches);
        };
        
        return false;
    }
}
