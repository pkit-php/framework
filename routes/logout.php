<?php

use Pkit\Abstracts\Route;
use Pkit\Auth\Session;
use Pkit\Http\Response;
use Pkit\Middlewares\Auth;

class Logout extends Route
{
    public $middlewares = [Auth::class => "Session"];

    public function logout(): Response
    {
        Session::logout();
        return new Response(["message" => "Logout complete"]);
    }

    public function GET($request): Response
    {
        return $this->logout();
    }

    public function POST($request): Response
    {
        return $this->logout();
    }
}

return new Logout;
