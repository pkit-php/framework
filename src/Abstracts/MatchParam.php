<?php

namespace Pkit\Abstracts;

class MatchParam
{

    public string
    $regex = "[^\/]+";
    public ?string
    $restRegex = ".+";

    public function validate(string $test)
    {
        return true;
    }

    public function convert(string $test)
    {
        return $test;
    }
}