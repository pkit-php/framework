<?php

use Pkit\Abstracts\Route;

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
