<?php

namespace Pkit\Auth;

class Session
{

  private static $time = 0;

  public static function init(int $time)
  {
    self::$time = $time;
  }

  private static function start()
  {
    if (session_status() != PHP_SESSION_ACTIVE) {
      session_start();
      session_regenerate_id();
      if (self::$time) {
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
}
