<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;

abstract class Middleware
{

  abstract public function handle(Request $request, \Closure $next, array $params);

}
