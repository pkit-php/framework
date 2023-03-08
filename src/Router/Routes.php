<?php

namespace Pkit\Router;

class Routes
{
    private static
    $patternVariable = '/\[\w+\]/',
    $patternInteger = '/\[\w+\:(int|integer)\]/',
    $patternFloat = '/\[\w+\:(float)\]/',
    $patternWord = '/\[\w+\:(word)\]/',
    $patternRest = '/\[\.{3}\w+\]/',
    $patternGeral = '/\[(?:\.{3})?(\w+)(?:\:(?:int|integer|float|word))?\]/';

    public static function matchRouteAndParams(string $route, string $uri, array &$array): bool
    {
        $variables = [];
        if (preg_match_all(self::$patternGeral, $route, $matches)) {
            $variables = $matches[1];
            $regex = [
                self::$patternInteger => "\3",
                self::$patternFloat => "\4",
                self::$patternVariable => "\2",
                self::$patternWord => "\5",
                self::$patternRest => "\1",
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
            "\1" => '(.+)',
            "\2" => '([^\/]+)',
            "\3" => '([0-9]+)',
            "\4" => '([0-9]+(?:\.[0-9])?)',
            "\5" => '(\w+)',
        ]);
        $route = '/^' . $route . '$/';

        if (preg_match($route, $uri, $matches)) {
            unset($matches[0]);
            $array = array_combine(@$variables ?? [], $matches);
            return true;
        }
        ;

        return false;
    }
}