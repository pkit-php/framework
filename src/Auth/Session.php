<?php

namespace Pkit\Auth;

use DateTime;
use Phutilities\Date;
use Pkit\Auth\Session\SessionEnv;

class Session extends SessionEnv
{
  private static function start()
  {
    if (session_status() != PHP_SESSION_ACTIVE) {
      session_save_path(self::getPath());
      session_start();
      if (self::getTime() && is_null($_SESSION['created'])) {
        $_SESSION['created'] = Date::format(new DateTime());
        setcookie(session_name(), session_id(), (time() + self::getTime()), '/', httponly: true);
      }
    }
  }

  public static function logged(): bool
  {
    self::start();
    return !is_null(@$_SESSION['payload']);
  }

  public static function login(mixed $payload)
  {
    self::start();
    $_SESSION['payload'] = $payload;
  }

  public static function logout()
  {
    self::start();
    setcookie(session_name());
    session_unset();
    session_destroy();
    session_write_close();
  }

  public static function getSession(): mixed
  {
    self::start();
    return $_SESSION['payload'];
  }

  public static function getCreated(): string
  {
    self::start();
    return $_SESSION['created'];
  }
}
