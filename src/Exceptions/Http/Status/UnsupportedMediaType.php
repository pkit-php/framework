<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class UnsupportedMediaType extends StatusException
{
    public function __construct(string $message, $th = null)
    {
        parent::__construct($message, Status::UNSUPPORTED_MEDIA_TYPE, $th);
    }
}