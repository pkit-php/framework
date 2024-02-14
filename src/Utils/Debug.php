<?php

namespace Phutilities;

use Pkit\Utils\Debug\DebugEnv;
use Pkit\Http\ContentType;

class Debug extends DebugEnv
{
    public static function console(mixed ...$arg)
    {
        if (!Debug::isDebug())
            return;
        var_dump(...$arg);
        if (Debug::isExitInDebug())
            exit;
    }

    public static function pde(mixed ...$arg)
    {
        if (!Debug::isDebug())
            return;
        echo "<pre>";
        var_dump(...$arg);
        echo "</pre>";
        if (Debug::isExitInDebug())
            exit;
    }

    public static function json(mixed $arg)
    {
        if (!Debug::isDebug())
            return;
        echo json_encode($arg);
        header("content-type:" . ContentType::JSON);
        if (Debug::isExitInDebug())
            exit;
    }
}
