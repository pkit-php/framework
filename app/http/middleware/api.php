<?php

class API
{
  public function handle($request, $response, $next)
  {
    $response->json();
    return $next($request, $response);
  }
}
