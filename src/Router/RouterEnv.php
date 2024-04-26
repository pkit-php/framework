<?php

namespace Pkit\Router;

class RouterEnv
{
    private static ?bool $subDomain = null;
    protected static ?string
    $routePath = null,
    $publicPath = null;

    public static function config(string $routePath, ?string $publicPath = null)
    {
        putenv("ROUTE_PATH=$routePath");
        if ($publicPath === null)
        putenv("PUBLIC_PATH=$publicPath");
    }

    public static function getRoutePath()
    {
        return getenv("ROUTES_PATH") ?: getcwd() . "/routes";
    }

    public static function getPublicPath()
    {
        return getenv("PUBLIC_PATH") ?: getcwd() . "/public";
    }

}