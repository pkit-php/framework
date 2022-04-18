<?php

function mathRoute($routes, $uri)
{
    $includeFile = '';
    $params = [];
    $variables = [];

    $patternRestVariable = '/{(.*?)}|\[(.*?)\]/';
    $patternVariable = '/{(.*?)}/';
    $patternRest = '/\[(.*?)\]/';
    foreach ($routes as $route => $file) {
        $variables = [];
        if (preg_match_all($patternRestVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(\d*\w*)', $route);
            $route = preg_replace($patternRest, '(.*?)', $route);
            $variables = array_merge($matches[1], $matches[2]);
            $variables = array_filter($variables, function ($var) {
                return $var !== '';
            });
        }


        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';
        if (preg_match($patternRoute, $uri)) {

            if (preg_match($patternRoute, $uri, $matches)) {
                unset($matches[0]);
                $params = array_combine($variables, $matches);
            };

            $includeFile = $file;
            break;
        };
    }

    return [$includeFile, $params];
}
