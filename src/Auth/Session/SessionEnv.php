<?php

namespace Pkit\Auth\Session;

use Phutilities\Env;

class SessionEnv
{
    private static ?int $time = null;
    private static ?string $path = null;

    public static function config(int $time, ?string $path = null)
    {
        self::$time = $time;
        self::$path = $path;
    }

    public static function getTime(): int
    {
        if (is_null(self::$time))
            self::$time = (int)Env::getEnvOrValue("SESSION_TIME", 0);
        return self::$time;
    }

    public static function getPath(): string
    {
        if (is_null(self::$path))
            self::$path = Env::getEnvOrValue("SESSION_PATH", session_save_path());
        return self::$path;
    }
}
