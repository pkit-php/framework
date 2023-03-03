<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares;
use Pkit\Middlewares\Cache as CacheMiddleware;

class Cache extends Route
{
    #[Middlewares([CacheMiddleware::class => ["cache_params" => ["key"]]])]
    public function GET($request): Response
    {
        sleep(1);
        $date = (new DateTime("now"));
        return new Response($date);
    }
}

return new Cache;