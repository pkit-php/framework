<?php

function mathRoute(array $routes, string $uri)
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
