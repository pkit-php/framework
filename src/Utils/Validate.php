<?php

namespace Pkit\Utils;

class Validate
{
  public static function email(string $email): bool
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  public static function url(string $url): bool
  {
    return filter_var($url, FILTER_VALIDATE_URL);
  }

  public static function ip(string $ip): bool
  {
    return filter_var($ip, FILTER_VALIDATE_IP);
  }

  public static function int(string $int): bool
  {
    return filter_var($int, FILTER_VALIDATE_INT);
  }

  public static function bool(string $bool): bool
  {
    return filter_var($bool, FILTER_VALIDATE_BOOL);
  }

  public static function float(string $float): bool
  {
    return filter_var($float, FILTER_VALIDATE_FLOAT);
  }

  public static function domain(string $domain): bool
  {
    return filter_var($domain, FILTER_VALIDATE_DOMAIN);
  }

  public static function regexp(string $regexp): bool
  {
    return filter_var($regexp, FILTER_VALIDATE_REGEXP);
  }
}
