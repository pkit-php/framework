<?php

namespace Pkit\Middlewares;

use DateTime;
use Pkit\Abstracts\Middleware;
use Pkit\Auth\Jwt as AuthJwt;
use Pkit\Http\Router;
use Pkit\Utils\Date;

class Jwt implements Middleware
{
    public function handle($request, $response, $next)
    {
        $token = AuthJwt::getBearer($request);
        if ($token && AuthJwt::validate($token)) {
            $expire = AuthJwt::getExpire();
            if ($expire) {
                $payload = AuthJwt::getPayload($token);
                $created = $payload->_created;
                if ($created) {
                    $delta = Date::deltaTime(
                        new DateTime($created),
                        new DateTime('now')
                    );
                    $interval = Date::dateIntervalToSeconds($delta);
                    if ($interval < $expire) {
                        return $next($request, $response);
                    }
                }
            } else {
                return $next($request, $response);
            }
        };
        $response->unauthorized();
        Router::runEspecialRoute();
    }
}
