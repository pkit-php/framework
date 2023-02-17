<?php

namespace Pkit\Exceptions\Auth;

use Pkit\Http\Status;

class UserUnauthorizedException extends AuthException
{
    public function __construct(bool $expired, string|null $authType = null)
    {
        if ($expired)
            parent::__construct($authType . ($authType ? $authType : "Auth") . " Expired", Status::UNAUTHORIZED, $authType);
        else
            parent::__construct(($authType ? "$authType: " : "") . "User Unauthorized", Status::UNAUTHORIZED, $authType);
    }
}