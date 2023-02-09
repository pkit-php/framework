<?php

use Pkit\Abstracts\EspecialRoute;
use Pkit\Http\Response;
use Pkit\Http\Router\Debug;

class FinalRoute extends EspecialRoute
{
    public function all($request, $th): Response
    {
        return Debug::error($request, $th);
    }
}

return new FinalRoute;
