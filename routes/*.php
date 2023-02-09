<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Router\Debug;

class FinalRoute extends Route
{
    public function ALL(Request $request, ?Throwable $th = null): Response
    {
        return Debug::error($request, $th);
    }
}

return new FinalRoute;