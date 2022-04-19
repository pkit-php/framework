<?php

use Pkit\Http\Route;

class UserById extends Route
{
  public function get($request, $response)
  {
    $params = $request->getRouter()->getParams();
    $response->json()->send($params);
  }
}

(new UserById)->run();
