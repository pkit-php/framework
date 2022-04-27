<?php

namespace Pkit\Utils;

use Pkit\Http\Response;

class Methods
{
  public static function methodNotAllowed($_, Response $response)
  {
    if (!$response->getModified()) {
      $response->onlyCode()->methodNotAllowed();
    }
    $response
      ->send();
  }
}
