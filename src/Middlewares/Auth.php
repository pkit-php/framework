<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Auth\Session;
use Pkit\Auth\Jwt as AuthJwt;
use Pkit\Exceptions\Http\Status\Unauthorized;
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
        $params = is_array($params) ? $params : [$params];
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

  private function tryAuth($auth, &$err)
  {
    try {
      return $auth();
    } catch (\Throwable $th) {
      if (
        $th->getFile() != __FILE__
        || $th->getTrace()[0]["function"] == "__construct"
      ) {
        throw $th;
      }
      $err = $th;
    }
  }

  private static function throwUserUnauthorized(bool $expired, string|null $authType = null)
  {
    if ($expired)
      throw new Unauthorized(($authType ? $authType : "Auth") . " Expired");
    else
      throw new Unauthorized("User Unauthorized");
  }

  private function authBySession($request, $next, $isGeneric)
  {
    $authType = $isGeneric ? null : "Session";
    if (!Session::logged()) {
      Session::logout();
      self::throwUserUnauthorized(false, $authType);
    }

    $expire = Session::getTime();
    if ($expire > 0) {
      $created = Session::getCreated();
      $interval =
        (new DateTime('now'))->getTimestamp() -
        (new DateTime($created))->getTimestamp();
      if ($interval > $expire) {
        Session::logout();
        self::throwUserUnauthorized(true, $authType);
      }
    }

    return $next($request);
  }

  private function authByJWT($request, $next, $isGeneric)
  {
    $token = AuthJwt::getBearer($request);
    $authType = $isGeneric ? null : "JWT";
    if (!$token || !AuthJwt::validate($token))
      self::throwUserUnauthorized(false, $authType);

    $expire = (int) getenv("JWT_EXPIRES") ?: 0;
    if ($expire > 0) {
      $created = AuthJwt::getPayload($token)->_created;
      $interval =
        (new DateTime('now'))->getTimestamp() -
        (new DateTime($created))->getTimestamp();
      if ($interval > $expire)
        self::throwUserUnauthorized(true, $authType);
    }
    return $next($request);
  }
}