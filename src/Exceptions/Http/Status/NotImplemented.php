<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class NotImplemented extends StatusException
{
    public function __construct(string $message, $th = null)
    {
        parent::__construct($message, Status::NOT_IMPLEMENTED, $th);
    }
}