<?php

namespace Pkit\Auth;

use DateTime;
use Pkit\Private\Env;
use Pkit\Utils\Date;

class Session
{

  private static ?int $time = null;

  public static function config(int $time)
  {
    self::$time = $time;
  }

  public static function getTime()
  {
    if (is_null(self::$time)) {
      self::$time = (int)Env::getEnvOrValue("SESSION_TIME", 0);
    }
    return self::$time;
  }

  private static function start()
  {
    if (session_status() != PHP_SESSION_ACTIVE) {
      session_start();
      session_regenerate_id();
      if (self::getTime() && !$_SESSION['created']) {
        $_SESSION['created'] = Date::format(new DateTime());
        setcookie(session_name(), session_id(), (time() + self::getTime()), '/', httponly: true);
      }
    }
  }

  public static function logged()
  {
    self::start();
    return !is_null(@$_SESSION['payload']);
  }

  public static function login($payload)
  {
    self::start();
    $_SESSION['payload'] = $payload;
  }

  public static function logout()
  {
    setcookie(session_name());
    session_unset();
    session_destroy();
  }

  public static function getSession()
  {
    self::start();
    return $_SESSION['payload'];
  }

  public static function getCreated()
  {
    self::start();
    return $_SESSION['created'];
  }
}
