<?php namespace Pkit\Utils;

use Pkit\Http\Response;

class Methods
{
  public static function methodNotAllowed($_, Response $response)
  {
    $response
      ->onlyCode()
      ->methodNotAllowed()
      ->send();
  }
}
