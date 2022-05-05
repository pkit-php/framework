<?php

namespace Pkit\Utils;

use DateTime;

class Date
{
    static public function deltaTime(DateTime $after, DateTime $before)
    {
        return $before->getTimestamp() - $after->getTimestamp();
    }
}
