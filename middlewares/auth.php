<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Http\Router;
use Pkit\Utils\Session;

class Auth implements Middleware
{
  public function handle($request, $response, $next)
  {
    if (Session::logged()) {
      return $next($request, $response);
    };
    $response->unauthorized();
    Router::runEspecialRoute();
  }
}
