<?php

namespace Pkit\Utils;

class Session
{

  private static $time;

  public static function setTimeSession(int $time)
  {
    self::$time = $time;
  }

  private static function init()
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
    Self::init();
    return !is_null($_SESSION['payload']);
  }

  public static function login($payload)
  {
    self::init();
    $_SESSION['payload'] = $payload;
  }

  public static function logout()
  {
    self::init();

    setcookie('PHPSESSID');
    session_regenerate_id();
    session_destroy();
    session_unset();
  }

  public static function getSession()
  {
    self::init();
    return $_SESSION['payload'];
  }
}
