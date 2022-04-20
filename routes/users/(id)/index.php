<?php

use App\Entitie\Users;
use Pkit\Http\Route;

class UserById extends Route
{
    public function get($request, $response)
    {
        $params = $request->getRouter()->getParams();
        $user = (new Users);
        $id = $params["id"];
        $selecteds = $user->select(['id:=' => $id]);
        echo $selecteds[0]->getProtectedValue('password') . "\n";
        $response->json()->send($selecteds[0]);
    }
}

(new UserById)->run();
