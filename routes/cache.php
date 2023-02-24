<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares;
use Pkit\Middlewares\Cache as CacheMiddleware;

class Cache extends Route
{
    #[Middlewares([CacheMiddleware::class])]
    public function GET($request): Response
    {
        $date = (new DateTime("now"));
        return new Response($date);
    }
}

return new Cache;