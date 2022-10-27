<?php

namespace Pkit\Middlewares;

use Exception;
use Phutilities\Debug as PhutilitiesDebug;
use ReflectionMethod;

class Debug extends PhutilitiesDebug
{
    public function __invoke($request, $next, $params)
    {
        if (is_null($params) || empty($params))
            $params = "console";
        if (!is_string($params))
            throw new Exception("Debug: command '" . $params . "' not is valid", 500);
        if (!in_array($params, ["console", "pde", "json"]))
            throw new Exception("Debug: command not is valid", 500);

        (new ReflectionMethod($this, $params))->invoke($this, $request);
        $next($request);
    }
}
