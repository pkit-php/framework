<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;

abstract class Middleware
{

  abstract public function __invoke(Request $request, \Closure $next, mixed $params);
}
