<?php

include $_SERVER['DOCUMENT_ROOT'] . '/app/abstracts/route.php';

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

function export()
{
    return new UserTeste;
};
