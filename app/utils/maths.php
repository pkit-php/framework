<?php

function mathRoute($routes, $uri)
{
    $includeFile = '';
    $params = [];

    $patternVariable = '/{(.*?)}/';
    foreach ($routes as $route => $file) {
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $variables = $matches[1];
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
