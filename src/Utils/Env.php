<?php

namespace Pkit\Utils;

class Env
{
  static function load(string $path): void
  {
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      $line = trim($line);
      if (strpos($line, '#') === 0) {
        continue;
      }
      $line = trim(explode("#", $line, 2)[0]);
      [$name, $value] = explode('=', $line, 2);
      $name = trim($name);
      $value = trim($value);

      if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
      }
    }
  }

  public static function getEnvOrValue(string $env, mixed $value)
  {
    $envValue = getenv($env, true);
    return $envValue
      ? $envValue
      : $value;
  }
}
