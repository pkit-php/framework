<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Http\Status;
use Pkit\Throwable\Redirect;

class Redirectable extends Route
{
    function get($request): Response
    {
        throw new Redirect("/", Status::MOVED_PERMANENTLY);
    }
}

return new Redirectable;
