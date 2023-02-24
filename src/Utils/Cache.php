<?php

namespace Pkit\Utils;

use Closure;
use Phutilities\Env;

class Cache
{

    private static ?string $dir = null;
    private static ?int $expiration = null;

    public function config(string $dir, int $expiration = null)
    {
        self::$dir = $dir;
        self::$expiration = $expiration;
    }
    public static function getCacheDir()
    {
        if (is_null(self::$dir))
            self::$dir = Env::getEnvOrValue('PKIT_CACHE_DIR', $_SERVER["DOCUMENT_ROOT"] . "/.pkit/cache");
        return self::$dir;
    }

    public static function getExpiration()
    {
        if (is_null(self::$expiration))
            self::$expiration = (int) Env::getEnvOrValue('PKIT_CACHE_TIME', 3600);
        return self::$expiration;
    }
    private static function getFilePath(string $hash)
    {
        $dir = self::getCacheDir();
        if (!file_exists($dir))
            mkdir($dir, 0777, true);

        return $dir . '/' . ltrim($hash, "/");
    }

    private static function storageCache(string $hash, $content)
    {
        $serialize = serialize($content);
        $cacheFilePath = self::getFilePath($hash);

        return file_put_contents($cacheFilePath, $serialize);
    }

    private static function getContentCache(string $hash)
    {
        $cacheFile = self::getFilePath($hash);
        if (!file_exists($cacheFile))
            return false;

        $createTime = filectime($cacheFile);
        $diffTime = time() - $createTime;
        if ($diffTime > self::getExpiration())
            return false;

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