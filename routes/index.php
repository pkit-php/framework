<?php

include $_SERVER['DOCUMENT_ROOT'] . '/app/abstracts/route.php';

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
