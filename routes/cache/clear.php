<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Middlewares;
use Pkit\Middlewares\Cache as CacheMiddleware;

return new class extends Route
{
    #[Middlewares([CacheMiddleware::class => ["invalidate" => ["/cache/*?key=1"]]])]
    public function GET($request): Response
    {
        $date = (new DateTime("now"));
        return new Response($date);
    }
};