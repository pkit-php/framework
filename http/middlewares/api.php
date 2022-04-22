<?php

namespace Pkit\Http\Middlewares;

use Pkit\Abstracts\Middleware;

class Api implements Middleware
{
  public function handle($request, $response, $next)
  {
    $response->json();
    return $next($request, $response);
  }
}
