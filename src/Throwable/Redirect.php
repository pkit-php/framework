<?php

namespace Pkit\Throwable;

use Pkit\Exceptions\Http\Status\InternalServerError;
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
            throw new InternalServerError(
                "Redirect: Status '$code' is not valid",
            );
        }
        parent::__construct($location, $code);
    }
}