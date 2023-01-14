<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares\Auth;

class Home extends Route
{

    public function get($request): Response
    {
        return new Response("home");
    }
}

return new Home;
