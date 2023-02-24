<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Http\Router;
use Pkit\Utils\Cache as CacheUtil;

class Cache extends Middleware
{
    public function __invoke($request, $next, $_)
    {
        return CacheUtil::getCache(Router::getUri(), fn() => $next($request));
    }
}