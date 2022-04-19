<?php

use Pkit\Http\Route;

class Users extends Route
{
    public function get($request, $response)
    {
        $response->send('user/index.php');
    }
}

(new Users)->run();
