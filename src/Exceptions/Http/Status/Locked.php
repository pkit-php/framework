<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class Locked extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::LOCKED, $th);
    }
}