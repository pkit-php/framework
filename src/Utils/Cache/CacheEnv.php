<?php

namespace Pkit\Utils\Cache;

class CacheEnv
{
    private static ?string $dir = null;
    private static ?int $expiration = null;

    public static function config(string $dir, int $expiration = 3600)
    {
        putenv("PKIT_CACHE_DIR=$dir");
        putenv("PKIT_CACHE_TIME=$expiration");
    }
    public static function getCacheDir()
    {
        return getenv('PKIT_CACHE_DIR') ?: getcwd() . "/.pkit/cache";
    }

    public static function getExpiration()
    {
        return (int) getenv('PKIT_CACHE_TIME') ?: 3600;
    }

}