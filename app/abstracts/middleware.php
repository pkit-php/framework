<?php

interface Middleware
{
  public function handle(Request $request, Response $response, Closure $next);
}
