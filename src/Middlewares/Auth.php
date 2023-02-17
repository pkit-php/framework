<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Auth\Session;
use Pkit\Auth\Jwt as AuthJwt;
use Pkit\Exceptions\Auth\UserUnauthorizedException;
use Phutilities\Date;
use Phutilities\Parse;
use DateTime;
use ReflectionMethod;

class Auth extends Middleware
{
  public function __invoke($request, $next, $params)
  {
    if (!is_array($params))
      if (is_null($params))
        $params = [];
      else
        $params = Parse::anyToArray($params);
    $isGeneric = count($params) > 1;

    if (empty($params)) {
      $params = ["Session", "JWT"];
      $isGeneric = true;
    }

    $lastTh = null;
    foreach ($params as $auth) {
      $return = $this->tryAuth(
        fn() => (new ReflectionMethod($this, "authBy" . $auth))
          ->invoke($this, $request, $next, $isGeneric),
        $err
      );
      if (is_null($err))
        return $return;
      else
        $lastTh = $err;
    }
    throw $lastTh;
  }

  public function tryAuth($auth, &$err)
  {
    try {
      return $auth();
    }
    catch (\Throwable $th) {
      if (
        $th->getFile() != __FILE__
        || $th->getTrace()[0]["function"] == "__construct"
      ) {
        throw $th;
      }
      $err = $th;
    }
  }

  public function authBySession($request, $next, $isGeneric)
  {
    $authType = $isGeneric ? null : "Session";
    if (!Session::logged()) {
      Session::logout();
      throw new UserUnauthorizedException(false, $authType);
    }

    $expire = Session::getTime();
    if ($expire > 0) {
      $created = Session::getCreated();
      $interval = Date::deltaTime(
        new DateTime($created),
        new DateTime('now')
      );
      if ($interval > $expire) {
        Session::logout();
        throw new UserUnauthorizedException(true, $authType);
      }
    }

    return $next($request);
  }

  public function authByJWT($request, $next, $isGeneric)
  {
    $token = AuthJwt::getBearer($request);
    $authType = $isGeneric ? null : "JWT";
    if (!$token || !AuthJwt::validate($token))
      throw new UserUnauthorizedException(false, $authType);

    $expire = AuthJwt::getExpire();
    if ($expire > 0) {
      $created = AuthJwt::getPayload($token)->_created;
      $interval = Date::deltaTime(
        new DateTime($created),
        new DateTime('now')
      );
      if ($interval > $expire)
        throw new UserUnauthorizedException(true, $authType);
    }
    return $next($request);
  }
}