<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Http\Router;

class FloatParams extends Route
{
    function GET($request): Response
    {
        return new Response(Router::getParams());
    }
}

return new FloatParams;
