<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class FieldsTooLarges extends StatusException
{
    public function __construct(string $message, $th = null)
    {
        parent::__construct($message, Status::FIELDS_TOO_LARGES, $th);
    }
}