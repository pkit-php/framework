<?php

namespace Pkit\Utils;

class Validate
{
  public static function email($email)
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  public static function url($url)
  {
    return filter_var($url, FILTER_VALIDATE_URL);
  }
}
