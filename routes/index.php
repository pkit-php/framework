<?php

use Pkit\Abstracts\Route;

class Index extends Route
{
  public function get($request, $response)
  {
    $response->send('index.php');
  }
}

function export()
{
  return new Index;
};
