<?php

namespace Pkit\Middlewares;

use DateTime;
use Pkit\Abstracts\Middleware;
use Pkit\Auth\Jwt as AuthJwt;
use Pkit\Http\Router;
use Pkit\Http\Status;
use Pkit\Utils\Date;

class Jwt extends Middleware
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
                    $interval = Date::deltaTime(
                        new DateTime($created),
                        new DateTime('now')
                    );
                    if ($interval < $expire) {
                        return $next($request, $response);
                    }
                }
            } else {
                return $next($request, $response);
            }
        };
        $response->status(Status::UNAUTHORIZED);
        Router::setMessage('User unauthorized');
        Router::runEspecialRoute();
    }
}
