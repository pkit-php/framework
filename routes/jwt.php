<?php

use Pkit\Abstracts\Route;
use Pkit\Auth\Jwt;
use Pkit\Exceptions\Http\Status\ExpectationFailed;
use Pkit\Http\Request;
use Pkit\Http\Response;

class Home extends Route
{
    public function GET(Request $request): Response
    {
        if (!is_array($request->postVars)) {
            if (!Jwt::validate($request->postVars))
                throw new ExpectationFailed;

            array(Jwt::getPayload($request->postVars));
            return new Response(Jwt::getPayload($request->postVars));
        }

        return new Response(Jwt::tokenize($request->postVars));
    }
}

return new Home;