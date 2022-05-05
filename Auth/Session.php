<?php

namespace Pkit\Auth;

class Session
{

  private static $time;

  public static function init(int $time)
  {
    self::$time = $time;
  }

  private static function start()
  {
    if (session_status() != PHP_SESSION_ACTIVE) {
      session_start();
      if (self::$time) {
        setcookie('PHPSESSID', session_id(), (time() + getenv('SESSION_TIME')), '/');
      }
    }
  }

  public static function logged()
  {
    Self::start();
    return !is_null(@$_SESSION['payload']);
  }

  public static function login($payload)
  {
    self::start();
    $_SESSION['payload'] = $payload;
  }

  public static function logout()
  {
    self::start();

    setcookie('PHPSESSID');
    session_regenerate_id();
    session_destroy();
    session_unset();
  }

  public static function getSession()
  {
    self::start();
    return $_SESSION['payload'];
  }
}
