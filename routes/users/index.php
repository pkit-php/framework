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


    public function post($request, $response)
    {
        $user = new EntitieUsers();
        $postVars = $request->getPostVars();

        $user->email = $postVars['email'];
        $user->name = $postVars['name'];
        $user->password = password_hash($postVars['password'], PASSWORD_DEFAULT);

        $user->insert();

        $response->json()->send(password_verify($postVars['password'], $user->password));
    }
}

(new Users)->run();
