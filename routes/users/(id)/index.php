<?php

use App\Entitie\Users;
use Pkit\Http\Route;

class UserById extends Route
{
    public function get($request, $response)
    {
        $params = $request->getRouter()->getParams();
        $id = $params["id"];

        $userEntity = (new Users);
        $user = $userEntity->select(['id:=' => $id])[0];

        if (empty($user)) {
            return $response->json()->notFound()->send([
                "error" => 404,
                "message" => 'user not found'
            ]);
        }

        return $response->json()->ok()->send([
            "user" => $user,
            "password" => $user->password
        ]);
    }
}

(new UserById)->run();
