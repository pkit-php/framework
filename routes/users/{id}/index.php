<?php

include $_SERVER['DOCUMENT_ROOT'] . '/app/abstracts/route.php';

class UserById extends Route
{
    public function get($request, $response)
    {
        $response->json()->send($request->getRouter()->getParams());
    }
}

function export()
{
    return new UserById;
};
