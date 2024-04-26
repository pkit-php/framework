<?php

namespace Pkit\Utils;

use Closure;
use Pkit\Exceptions\Cache\CacheFilePermissionDenied;
use Pkit\Utils\Cache\CacheEnv;

class Cache extends CacheEnv
{
    private static function parseFilePath(string $hash)
    {
        $dir = self::getCacheDir();
        if (!file_exists($dir))
            mkdir($dir, 0777, true);

        return $dir . '/' . ltrim($hash, "/");
    }

    private static function storageCache(string $hash, $content)
    {
        $serialize = serialize($content);
        $cacheFilePath = self::parseFilePath($hash);

        if (!@file_put_contents($cacheFilePath, $serialize))
            throw new CacheFilePermissionDenied($cacheFilePath);

    }

    private static function getContentCache(string $hash)
    {
        $cacheFile = self::parseFilePath($hash);
        if (!file_exists($cacheFile))
            return false;

        if (self::getExpiration() > 0) {
            $createTime = filectime($cacheFile);
            $diffTime = time() - $createTime;
            if ($diffTime > self::getExpiration())
                return false;
        }

        $serialize = file_get_contents($cacheFile);
        return unserialize($serialize);
    }

    public static function getCache(string $hash, Closure $function)
    {
        if ($content = self::getContentCache($hash))
            return $content;

        $content = $function();
        self::storageCache($hash, $content);

        return $content;
    }
}