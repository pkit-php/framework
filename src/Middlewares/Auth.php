<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Http\Router;
use Pkit\Auth\Session;
use Pkit\Http\Status;
use Pkit\Utils\Date;

class Auth implements Middleware
{
  public function handle($request, $response, $next)
  {
    if (Session::logged()) {
      $expire = Session::getTime();
      if ($expire) {
        $created = Session::getCreated();
        if ($created) {
          $interval = Date::deltaTime(
            new \DateTime($created),
            new \DateTime('now')
          );
          if ($interval < $expire) {
            return $next($request, $response);
          }
        }
      } else {
        return $next($request, $response);
      }
    } else {
      Session::logout();
    }
    $response->status(Status::UNAUTHORIZED);
    Router::setMessage('User unauthorized');
    Router::runEspecialRoute();
  }
}
