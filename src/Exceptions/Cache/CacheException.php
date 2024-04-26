<?php

namespace Pkit\Exceptions\Cache;

use Pkit\Exceptions\PkitException;

class CacheException extends PkitException
{
    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);
    }
}