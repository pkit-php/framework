<?php

use Pkit\Abstracts\Route;
use Pkit\Auth\Jwt;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Status;
use Pkit\Middlewares\Auth;
use Pkit\Throwable\Error;

class Home extends Route
{
    public function GET(Request $request): Response
    {
        if (!is_array($request->postVars)) {
            if (!Jwt::validate($request->postVars))
                throw new Error("", Status::EXPECTATION_FAILED);

            array(Jwt::getPayload($request->postVars));
            return new Response(Jwt::getPayload($request->postVars));
        }

        return new Response(Jwt::tokenize($request->postVars));
    }
}

return new Home;