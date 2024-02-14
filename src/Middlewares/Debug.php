<?php

namespace Pkit\Middlewares;

use Exception;
use Pkit\Utils\Debug as PhutilitiesDebug;
use ReflectionMethod;

class Debug
{
    public function __invoke($request, $next, $params)
    {
        if (is_null($params) ?: empty($params))
            $params = "console";
        if (!is_string($params))
            throw new Exception("Debug: command '" . $params . "' not is valid", 500);
        if (!in_array($params, ["console", "pde", "json"]))
            throw new Exception("Debug: command not is valid", 500);

        (new ReflectionMethod(PhutilitiesDebug::class, $params))
            ->invoke(new PhutilitiesDebug, $request);
        $next($request);
    }
}
