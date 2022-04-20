<?php namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;

interface Middleware
{
  public function handle(Request $request, Response $response, \Closure $next);
}
