<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class InternalServerError extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::INTERNAL_SERVER_ERROR, $th);
    }
}