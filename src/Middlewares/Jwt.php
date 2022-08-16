<?php

namespace Pkit\Middlewares;

use DateTime;
use Pkit\Abstracts\Middleware;
use Pkit\Auth\Jwt as AuthJwt;
use Pkit\Http\Status;
use Pkit\Throwable\Error;
use Phutilities\Date;

class Jwt extends Middleware
{
    public function handle($request, $next, $_)
    {
        $token = AuthJwt::getBearer($request);
        if (!$token || !AuthJwt::validate($token))
            throw new Error("Jwt: User Unauthorized", Status::UNAUTHORIZED);

        $expire = AuthJwt::getExpire();
        if (AuthJwt::getExpire() > 0) {
            $created = AuthJwt::getPayload($token)->_created;
            $interval = Date::deltaTime(
                new DateTime($created),
                new DateTime('now')
            );
            if ($interval > $expire)
                throw new Error("Jwt: Token Expired", Status::UNAUTHORIZED);
        }
        return $next($request);
    }
}
