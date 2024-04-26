<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Router;

class restFilenameParams extends Route
{
    function GET($request): Response
    {
        return new Response(Router::getParams());
    }
}

return new restFilenameParams;