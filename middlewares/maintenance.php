<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Http\Router;
use Pkit\Http\Status;

class Maintenance implements Middleware
{
  public function handle($request, $response, $next)
  {
    $response->status(Status::SERVICE_UNAVAILABLE);
    Router::runEspecialRoute();
  }
}
