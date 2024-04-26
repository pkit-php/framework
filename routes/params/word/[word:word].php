<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Router;

class wordParams extends Route
{
    function GET($request): Response
    {
        return new Response(Router::getParams());
    }
}

return new wordParams;