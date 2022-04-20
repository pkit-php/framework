<?php namespace Pkit\Http\Middleware;

use Pkit\Abstracts\Middleware;

class API implements Middleware
{
  public function handle($request, $response, $next)
  {
    $response->json();
    return $next($request, $response);
  }
}
