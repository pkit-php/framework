<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Http\Router;

class Maintenance implements Middleware
{
  public function handle($request, $response, $next)
  {
    $response->serviceUnavailable();
    Router::runEspecialRoute();
  }
}
