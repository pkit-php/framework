<?php

namespace Pkit\Utils\Debug;


class DebugEnv
{
    private static ?bool
        $isDebug = null,
        $exitInDebug = null;

    protected static function config()
    {
    }

    protected static function isDebug()
    {
        if (is_null(self::$isDebug))
            self::$isDebug = getenv("PKIT_DEBUG") ?: "false" == "true";
        return self::$isDebug;
    }

    protected static function isExitInDebug()
    {
        if (is_null(self::$exitInDebug))
            self::$exitInDebug = getenv("PKIT_EXIT_IN_DEBUG") ?: "true" == "true";
        return self::$exitInDebug;
    }
}
