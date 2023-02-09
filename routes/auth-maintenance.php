<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares;
use Pkit\Middlewares\Auth;
use Pkit\Middlewares\Maintenance;

class MaintenanceAuthenticated extends Route
{

    #[Middlewares([
    Auth::class => "Session",
    Maintenance::class,
    ])]
    function GET($request): Response
    {
        return new Response("get");
    }
}

return new MaintenanceAuthenticated;