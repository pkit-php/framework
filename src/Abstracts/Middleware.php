<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;

abstract class Middleware
{
  protected ?array $params = null;

  abstract public function handle(Request $request, Response $response, \Closure $next);

  public function setParams($params)
  {
    $this->params = $params;
  }

  public function getParams()
  {
    return $this->params;
  }
}
