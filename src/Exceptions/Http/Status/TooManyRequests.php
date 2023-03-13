<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class TooManyRequests extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::TOO_MANY_REQUESTS, $th);
    }
}