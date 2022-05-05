<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Http\ContentType;

class Api implements Middleware
{
  public function handle($request, $response, $next)
  {
    $response->contentType(ContentType::NONE);
    return $next($request, $response);
  }
}
