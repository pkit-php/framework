<?php

function mathRoute(array $routes, string $uri)
{
    $includeFile = '';
    $params = [];
    $variables = [];

    $patternRestVariable = '/{(.*?)}|\[(.*?)\]/';
    $patternVariable = '/{(.*?)}/';
    $patternRest = '/\[(.*?)\]/';

    foreach ($routes as $route => $file) {
        $variables = [];
        $route = str_replace('.', '\.', $route);
        $route = '/^' . str_replace('/', '\/', $route) . '$/';
        if (preg_match_all($patternRestVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(\d*\w*)', $route);
            $route = preg_replace($patternRest, '(.*)', $route);
            $variables = $matches[0];
            $variables = array_map(function ($var) {
                $var = preg_replace('/[{}\[\]]/', '', $var);
                return $var;
            }, $variables);
        }
        $patternRoute = $route;


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
