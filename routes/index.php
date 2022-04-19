<?php

use Pkit\Http\Route;

class Index extends Route
{
  public function get($request, $response)
  {
    $response->send('index.php');
  }
}

(new Index)->run();
