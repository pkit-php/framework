<?php

include $_SERVER['DOCUMENT_ROOT'] . '/app/abstracts/route.php';

class Teste extends Route
{
    public function get($request, $response)
    {
        $response->send('teste.php');
    }
}

function export()
{
    return new Teste;
};
