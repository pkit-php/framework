<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares\Maintenance as MiddlewaresMaintenance;

class Maintenance extends Route
{
    public $middlewares = [
        "post" => MiddlewaresMaintenance::class,
    ];

    function get($request): Response
    {
        return new Response("get");
    }

    function post($request): Response
    {
        return new Response("post");
    }
}

return new Maintenance;
