<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares;
use Pkit\Middlewares\Maintenance as MiddlewaresMaintenance;

class Maintenance extends Route
{

    function GET($request): Response
    {
        return new Response("get");
    }

    #[Middlewares([MiddlewaresMaintenance::class])]
    function POST($request): Response
    {
        return new Response("post");
    }
}

return new Maintenance;