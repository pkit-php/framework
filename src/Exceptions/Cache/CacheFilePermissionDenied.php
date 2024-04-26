<?php

namespace Pkit\Exceptions\Cache;

class CacheFilePermissionDenied extends CacheException
{
    public function __construct(public readonly string $fileCache)
    {
        parent::__construct("Failed to open stream: Permission denied in $fileCache");
    }
}