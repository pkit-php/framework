<?php

use Pkit\Abstracts\Route;

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
