<?php

namespace Pkit\Exceptions\Http\Status;

use Pkit\Http\Status;

class VariantAlsoNegotiates extends StatusException
{
    public function __construct(string $message = "", $th = null)
    {
        parent::__construct($message, Status::VARIANT_ALSO_NEGOTIATES, $th);
    }
}