<?php

namespace Pkit\Auth\Jwt;

use Phutilities\Env;

class JwtEnv
{
    private static ?string $key = null;
    private static ?int $expire = null;
    private static ?string $alg = null;


    public static function config(string $key, $expire = 0, $alg = 'HS256')
    {
        self::$key = $key;
        self::$expire = $expire;
        self::$alg = $alg;
    }

    public static function getAlg(): string
    {
        if (is_null(self::$alg))
            self::$alg = Env::getEnvOrValue("JWT_ALG", 'HS256');
        return self::$alg;
    }

    public static function getExpire(): int
    {
        if (is_null(self::$expire))
            self::$expire = (int)Env::getEnvOrValue("JWT_EXPIRES", 0);
        return self::$expire;
    }

    public static function getKey(): string
    {
        if (is_null(self::$key))
            self::$key = Env::getEnvOrValue("JWT_KEY", "");
        return self::$key;
    }
}
