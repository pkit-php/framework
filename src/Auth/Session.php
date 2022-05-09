<?php

namespace Pkit\Auth;

use DateTime;
use Pkit\Utils\Date;

class Session
{

  private static $time = 0;

  public static function config(int $time)
  {
    self::$time = $time;
  }

  public static function getTime()
  {
    return self::$time;
  }

  private static function start()
  {
    if (session_status() != PHP_SESSION_ACTIVE) {
      session_start();
      session_regenerate_id();
      if (self::$time && !$_SESSION['created']) {
        $_SESSION['created'] = Date::format(new DateTime());
        setcookie(session_name(), session_id(), (time() + self::$time), '/', httponly: true);
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
