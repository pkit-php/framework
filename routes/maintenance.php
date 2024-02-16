<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares;
use Pkit\Middlewares\Maintenance as MiddlewaresMaintenance;

return new class extends Route
{

    #[Middlewares([MiddlewaresMaintenance::class])]
    function GET($request): Response
    {
        return new Response("get");
    }

};