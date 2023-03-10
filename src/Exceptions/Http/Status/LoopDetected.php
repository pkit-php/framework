<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class LoopDetected extends StatusException
{
    public function __construct(string $message, $th = null)
    {
        parent::__construct($message, Status::LOOP_DETECTED, $th);
    }
}