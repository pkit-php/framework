<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class MethodNotAllowed extends StatusException
{
    public function __construct(string $message, $th = null)
    {
        parent::__construct($message, Status::METHOD_NOT_ALLOWED, $th);
    }
}