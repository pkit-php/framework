<?php

namespace Pkit\Throwable;

use Pkit\Http\Status;
use Throwable;

class Error extends \Exception
{
    public function __construct(string $message, int $code, Throwable|null $th = null)
    {
        if (
            $code < 400 ||
            $code >= 600 ||
            !Status::validate($code)
        ) {
            throw new Error(
                "Error: Status '$code' is not valid",
                Status::INTERNAL_SERVER_ERROR,
                $th
            );
        }
        parent::__construct($message, $code, $th);
    }
}
