<?php

namespace Pkit\Throwable;

use Pkit\Http\Status;

class Redirect extends \Exception
{
    public function __construct(string $location, $code = 301)
    {
        if (
            $code < 300 ||
            $code >= 400 ||
            !Status::validate($code)
        ) {
            throw new Error(
                "Redirect: Status '$code' is not valid",
                Status::INTERNAL_SERVER_ERROR
            );
        }
        parent::__construct($location, $code);
    }
}
