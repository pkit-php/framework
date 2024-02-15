<?php

use Pkit\Abstracts\Route;
use Pkit\Auth\Session;
use Pkit\Exceptions\Http\Status\NotAcceptable;
use Pkit\Exceptions\Http\Status\Unauthorized;
use Pkit\Http\Response;

class Login extends Route
{
    public function GET($request): Response
    {
        return $this->getLoginPage();
    }

    public function getLoginPage($code = 200)
    {
        return Response::render("login", $code, [
            "title" => "login",
            "code" => $code
        ]);
    }

    public function POST($request): Response
    {
        $login = [
            "email" => $request->postVars["email"],
            "password" => $request->postVars["password"]
        ];

        if (
            strlen($login["email"] ?? "") <= 0 ||
            strlen($login["password"] ?? "") <= 0
        ) {
            throw new NotAcceptable("Email e Senha obrigatórios");
        }


        if ($login["email"] != "email@email.com" || $login["password"] != "123") {
            throw new Unauthorized("Email ou senha inválidos");
        }

        Session::login($login);
        return new Response($login);
    }
}

return new Login;