<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares\Maintenance as MiddlewaresMaintenance;

class Maintenance extends Route
{
    public $middlewares = [
        "post" => MiddlewaresMaintenance::class,
    ];

    function GET($request): Response
    {
        return new Response("get");
    }

    function POST($request): Response
    {
        return new Response("post");
    }
}

return new Maintenance;
