<?php

namespace Pkit\Private;

class Env
{
  public static function getEnvOrValue(string $env, mixed $value)
  {
    $envValue = getenv($env, true);
    return $envValue
      ? $envValue
      : $value;
  }
}
