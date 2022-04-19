<?php

use Pkit\Http\Route;

class Teste extends Route
{
    public function get($request, $response)
    {
        $response->send('teste.php');
    }
}

(new Teste)->run();
