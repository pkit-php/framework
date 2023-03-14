<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Exceptions\Http\Status\ServiceUnavailable;


class Maintenance extends Middleware
{
  public function __invoke($request, $next, $_)
  {
    throw new ServiceUnavailable("page '$request->uri' in maintenance");
  }
}