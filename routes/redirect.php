<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Response;
use Pkit\Http\Status;
use Pkit\Throwable\Redirect;

class Redirectable extends Route
{
    function GET($request): Response
    {
        throw new Redirect("/", Status::MOVED_PERMANENTLY);
    }
}

return new Redirectable;
