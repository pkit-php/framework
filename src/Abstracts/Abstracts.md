# Abstracts

São modelos bases para criação de Middlewares e Rotas

## Middleware

A abstração de Middleware é implementada através do handle interceptando o request e next, assim chamando o next de acordo com a condições e necessidades, como por exemplo:

  ```php
  <?php
  // .../app/middlewares/json.php
  namespace App\Middlewares;

  use Pkit\Http\Request;
  use Pkit\Http\Response;
  use PKit\Http\ContentType;
  use PKit\Abstracts\Middleware;

  class Test extends Middleware
  {
    public function handle(Request $request, \Closure $next){
      echo "test";
      return $next($request);
    };
  }
  ```

## Route

A abstração de rotas funcionam a partir dos métodos das classes, sendo executado o método com o mesmo nome do método do cabeçalho HTTP, recebendo o request, assim retornado o conteúdo final, como por exemplo:

  ```php
  <?php

  use Pkit\Abstracts\Route;
  use Pkit\Auth\Session;
  use Pkit\Http\Response;
  use Pkit\Http\Status;
  use Pkit\Middlewares\Maintenance;
  use Pkit\Middlewares\Auth;
  use Pkit\Throwable\Redirect;

  class Login extends Route
  {
    public $middlewares = [
      Maintenance::class,
      'GET' => [
        Auth::class
      ]
    ];

    public function GET($request)
    {
      return new Response(Session::getSession());
    }

    public function POST($request, $response)
    {
      /*validação*/

      Session::login($user);
      throw new Redirect("/");
    }
  }

  return new Login;
  ```
