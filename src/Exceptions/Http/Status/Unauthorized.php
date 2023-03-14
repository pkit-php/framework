<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class Unauthorized extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::UNAUTHORIZED, $th);
    }
}