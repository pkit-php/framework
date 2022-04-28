<?php

namespace Pkit\Utils;

use Pkit\Http\Response;
use Pkit\Http\Status;

class Methods
{
  public static function methodNotAllowed($_, Response $response)
  {
    $response
      ->onlyCode()
      ->setStatus(Status::METHOD_NOT_ALLOWED)
      ->send();
  }
}
