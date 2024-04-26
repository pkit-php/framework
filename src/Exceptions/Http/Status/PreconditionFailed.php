<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class PreconditionFailed extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::PRECONDITION_FAILED, $th);
    }
}