<?php

use Pkit\Abstracts\Route;
use Pkit\Auth\Session;
use Pkit\Http\Response;
use Pkit\Http\Status;
use Pkit\Throwable\Error;
use Pkit\Utils\View;

class Login extends Route
{
    public function GET($request): Response
    {
        return $this->getLoginPage();
    }

    public function getLoginPage($code = 200)
    {
        return new Response(View::layout("login", [
            "title" => "login",
            "code"  => $code
        ]), $code);
    }

    public function POST($request): Response
    {
        $login = [
            "email"    => $request->postVars["email"],
            "password" => $request->postVars["password"]
        ];

        if (
            strlen($login["email"] ?? "") <= 0 ||
            strlen($login["password"] ?? "") <= 0
        ) {
            throw new Error("Email e Senha obrigatórios", Status::NOT_ACCEPTABLE);
        }

        $messageInvalid = "Email ou senha invalida";

        if ($login["email"] != "email@email.com" || $login["password"] != "123") {
            throw new Error($messageInvalid, Status::UNAUTHORIZED);
        }

        Session::login($login);
        return new Response($login);
    }
}

return new Login;