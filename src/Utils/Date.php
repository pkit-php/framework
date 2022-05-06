<?php

namespace Pkit\Utils;

use DateTime;

class Date
{
    public static  function deltaTime(DateTime $after, DateTime $before)
    {
        return $before->getTimestamp() - $after->getTimestamp();
    }

    public static function format(DateTime $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
