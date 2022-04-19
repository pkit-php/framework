<?php
include __DIR__ . '/../../abstracts/middleware.php';
class API implements Middleware
{
  public function handle($request, $response, $next)
  {
    $response->json();
    return $next($request, $response);
  }
}
