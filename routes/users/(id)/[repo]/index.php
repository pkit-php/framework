<?php

include $_SERVER['DOCUMENT_ROOT'] . '/app/abstracts/route.php';

class UserById extends Route
{
  public function get($request, $response)
  {
    $params = $request->getRouter()->getParams();
    $response->json()->send($params);
  }
}

function export()
{
  return new UserById;
};
