<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class BadGateway extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::BAD_GATEWAY, $th);
    }
}