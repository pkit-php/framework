<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares\Auth;
use Pkit\Middlewares\Maintenance;

class MaintenanceAuthenticated extends Route
{
    public $middlewares = [
        Auth::class => "Session",
        Maintenance::class,
    ];

    function get($request): Response
    {
        return new Response("get");
    }
}

return new MaintenanceAuthenticated;
