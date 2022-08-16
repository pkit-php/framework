<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Auth\Session;
use Pkit\Http\Status;
use Pkit\Throwable\Error;
use Phutilities\Date;

class Auth extends Middleware
{
  public function handle($request, $next, $_)
  {
    if (!Session::logged()) {
      Session::logout();
      throw new Error("Session: User Unauthorized", Status::UNAUTHORIZED);
    }

    $expire = Session::getTime();
    if ($expire > 0) {
      $created = Session::getCreated();
      $interval = Date::deltaTime(
        new \DateTime($created),
        new \DateTime('now')
      );
      if ($interval > $expire) {
        Session::logout();
        throw new Error("Session: Session Expired", Status::UNAUTHORIZED);
      }
    }

    return $next($request);
  }
}
