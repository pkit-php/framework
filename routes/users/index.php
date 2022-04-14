<?php

include $_SERVER['DOCUMENT_ROOT'] . '/app/abstracts/route.php';

class Users extends Route
{
    public function get($request, $response)
    {
        $response->send('user/index.php');
    }
}

function export()
{
    return new Users;
};
