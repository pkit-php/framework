<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class BadRequest extends StatusException
{
    public function __construct(string $message, $th = null)
    {
        parent::__construct($message, Status::BAD_REQUEST, $th);
    }
}