<?php

use Pkit\Http\Route;

class UserById extends Route
{
    public function get($request, $response)
    {
        $response->json()->send($request->getRouter()->getParams());
    }
}

(new UserById)->run();
