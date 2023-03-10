<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class RequestRangeNotSatisfiable extends StatusException
{
    public function __construct(string $message, $th = null)
    {
        parent::__construct($message, Status::REQUEST_RANGE_NOT_SATISFIABLE, $th);
    }
}