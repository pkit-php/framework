<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class HttpVersionNotSupported extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::HTTP_VERSION_NOT_SUPPORTED, $th);
    }
}