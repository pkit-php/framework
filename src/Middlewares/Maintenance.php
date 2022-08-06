<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Http\Status;
use Pkit\Throwable\Error;

class Maintenance extends Middleware
{
  public function handle($request, $next, $_)
  {
    throw new Error("page in maintenance", Status::SERVICE_UNAVAILABLE);
  }
}
