<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Router;

return new class extends Route
{
    function GET($request): Response
    {
        return new Response(Router::getParams());
    }
};