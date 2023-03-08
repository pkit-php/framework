<?php

namespace Pkit\Router;

use Phutilities\Env;

class RouterEnv
{
    private static ?bool $subDomain = null;
    protected static ?string
    $routePath = null,
    $publicPath = null;

    public static function config(string $routePath, ?string $publicPath = null, bool $subDomain = false)
    {
        self::$routePath = rtrim($routePath, "/");
        self::$publicPath = $publicPath
            ? rtrim($publicPath, "/")
            : null;
        self::$subDomain = $subDomain;
    }

    public static function getRoutePath()
    {
        if (is_null(self::$routePath))
            self::$routePath = Env::getEnvOrValue("ROUTES_PATH", $_SERVER["DOCUMENT_ROOT"] . "/routes");
        return self::$routePath;
    }

    public static function getPublicPath()
    {
        if (is_null(self::$publicPath))
            self::$publicPath = Env::getEnvOrValue("PUBLIC_PATH", $_SERVER["DOCUMENT_ROOT"] . "/public");
        return self::$publicPath;
    }

    public static function getSubDomain()
    {
        if (is_null(self::$subDomain))
            self::$subDomain = Env::getEnvOrValue("SUB_DOMAIN", "false") == "true";
        return self::$subDomain;
    }
}