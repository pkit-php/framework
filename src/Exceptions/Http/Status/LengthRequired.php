<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class LengthRequired extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::LENGTH_REQUIRED, $th);
    }
}