# Abstracts

São modelos bases para criação de Middlewares e Rotas

## Middleware

- A abstração deve ser implementada para que funcione da maneira desejada
- Funciona com o handle interceptando o request, response e next, assim chamando o next de acordo com a condições e necessidades
- exemplo:

  ```php
  <?php
  // .../app/middlewares/json.php
  namespace App\Middlewares;

  use Pkit\Http\Request;
  use Pkit\Http\Response;
  use PKit\Http\ContentType;

  class Json implements Middleware
  {
    public function handle(Request $request, Response $response, \Closure $next){
      $request->setContentType(ContentType::JSON);
      $next($request, $response);
    };
  }
  ```

## Route

- A abstração deve ser estendida para que funcione da maneira desejada
- O middlewares são opcionais e são executados de acordo com as chaves:
  - caso não aja será usado em todos o métodos;
  - caso aja será usado no método com o mesmo nome da chave;
- É executado o método com o mesmo nome do método do cabeçalho HTTP, recebendo o request e o response, assim enviando o conteúdo final
- exemplo:

  ```php
  <?php

  use Pkit\Abstracts\Route;
  use Pkit\Auth\Session;
  use Pkit\Http\Status;

  class Index extends Route
  {
    public $middlewares = [
      "pkit/maintenance",
      'get' => [
        "pkit/auth",
        "pkit/api"
      ],
      'post' => "pkit/onlycode"
    ];

    public function get($request, $response)
    {
      $request->send(Session::getSession());
    }

    public function post($request, $response)
    {
      /*validação*/

      Session::login($user);
      $response->headers['Location'] = "/";
      $response->sendStatus(Status::OK);
    }
  }

  (new Index)->run();
  ```
