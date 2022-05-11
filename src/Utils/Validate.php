<?php

namespace Pkit\Utils;

class Validate
{
  public static function email(string $email)
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  public static function url(string $url)
  {
    return filter_var($url, FILTER_VALIDATE_URL);
  }
}
