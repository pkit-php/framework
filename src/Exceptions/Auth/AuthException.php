<?php

namespace Pkit\Exceptions\Auth;

use Pkit\Exceptions\PkitException;

class AuthException extends PkitException
{
    public function __construct($message = "", $code = 0, public readonly string|null $authType = null)
    {
        parent::__construct($message, $code);
    }
}