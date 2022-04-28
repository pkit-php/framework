<?php

namespace Pkit\Utils;

use DateInterval;
use DateTime;

class Date
{
    static public function dateIntervalToSeconds(DateInterval $delta)
    {
        return ($delta->s)
            + ($delta->i * 60)
            + ($delta->h * 60 * 60)
            + ($delta->d * 60 * 60 * 24)
            + ($delta->m * 60 * 60 * 24 * 30)
            + ($delta->y * 60 * 60 * 24 * 365);
    }

    static public function deltaTime(DateTime $after, DateTime $before)
    {
        return $after->diff($before);
    }
}
