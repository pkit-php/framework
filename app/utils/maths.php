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
        if (preg_match_all($patternRestVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(\d*\w*)', $route);
            $route = preg_replace($patternRest, '(.*?)', $route);
            $variables = array_merge($matches[1], $matches[2]);
            foreach ($variables as $key => $value) {
                if ($value == '') {
                    unset($variables[$key]);
                }
            }
        }


        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';
        echo $patternRoute;
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
