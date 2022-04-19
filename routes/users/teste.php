<?php

use Pkit\Http\Route;

class UserTeste extends Route
{
    public $middlewares = [
        'get' => ['api'],
    ];

    public function get($request, $response)
    {
        $response->send('user/teste.php');
    }

    public function post($request, $response)
    {
        $response->send('user\teste.php');
    }
}

(new UserTeste)->run();
