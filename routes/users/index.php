<?php

use App\Entitie\Users as EntitieUsers;
use Pkit\Http\Route;

class Users extends Route
{
    public function get($request, $response)
    {
        $user = (new EntitieUsers())->select();

        return $response->json()->ok()->send($user);
    }
}

(new Users)->run();
