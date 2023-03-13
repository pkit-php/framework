<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class UnprocessableEntity extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::UNPROCESSABLE_ENTITY, $th);
    }
}