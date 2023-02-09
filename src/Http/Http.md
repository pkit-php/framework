# Http

Comportamento geral em relação a comportamentos relacionados ao Protocolo HTTP

## Router

- Gestor e inicializador de rotas estáticas e dinâmicas(php)
- O `index.php` sempre se referem as pasta que se encontram
- Gerencia URI dinâmica com base em parâmetros de rotas:

  - `[abc]` 'qualquer coisa entre `/`'
  - `{...abc}` 'qualquer coisa'

    ```php
    .htaccess
    index.php
    pkit/
    routes/
    ├ (id)/
    │ ├ [repo]/
    │ │ ├ {file}.php
    │ │ └ index.php
    │ └ index.php
    ├ home.php
    └ index.php
    ```

- Rota especial

  - Rota que intercepta erros de rotas ou chamadas intencionais a mesma, ainda funcionando como um rota comum. Essa rota deve ser chamada de `*.php` e estar no pasta routes.
  - uso:
  
    ```php
    <?php
    //.../*
    use Pkit\Http\Router;
    /***/
    $params = Router::runEspecialRoute();//:array
    /***/
    ```

- Guarda os parâmetros passados na URI antes configurados pelas rotas

  ```php
  <?php
  //.../*
  use Pkit\Http\Router;
  /***/
  $params = Router::getParams();//:array
  /***/
  ```

- configuração/uso:

  ```php
  <?php
  //.../index.php
  require __DIR__ . '/pkit/load.php';

  use Pkit\Http\Router;
  /***/
  Router::config(__DIR__ . '/routes');
  Router::run();

  ```

## Route

- Gestor de execução de código por método http
- Usam os middlewares configurados
- exemplo/uso:

  ```php
  <?php
  //.../*
  # A abstração do Route estende o Route do HTTP
  use Pkit\Abstracts\Route;
  use Pkit\Auth\Session;
  use Pkit\Http\Status;
  use Pkit\Http\Request;
  use Pkit\Http\Response;
  use Pkit\Utils\View;
  /***/

  class Login extends Route
  {
    # Ele que comanda a execução dos Middlewares
    public $middlewares = [
      "get" => [
        "pkit/auth",
        "pkit/api",
      ],
      "post" => "pkit/onlycode"
    ];

    public function GET(Request $request, Response $response)
    {
      $response->send(Session::getSession());
    }

    public function POST(Request $request, Response $response)
    {
      /*validação*/
      Session::login($user);
      $response->headers['Location'] = "/";
      $response->sendStatus(Status::OK);
    }
  }

  # É executado de forma estática
  Login::run();

  ```

  - Referências:
    - [Route Abstraction](../abstracts/Abstracts.md)

## Request

- Armazena as informações relacionado a requisição
- Suporta conteúdos em `json/xml/form` desde que esteja explicito na requisição
- uso :

  ```php
  <?php
  //.../*
  use use Pkit\Http\Request;
  /***/
  $request = new Request;
  /***/
  $postVars = $request->postVars;//:array
  /***/
  $queryParams = $request->queryParams;//:array
  /***/
  $headers = $request->headers;//:array
  /***/
  ```

## Response

- Configura as informações a serem enviadas
- Suporta conteúdos em `json/html/form` desde que esteja explicito
- uso:

  ```php
  <?php
  //.../*
  use Pkit\Http\Request;
  /***/
  $request = new Response;
  /***/
  $request->contentType(/*content-type:string*/);
  # setContentType somente se o mesmo já não tenha sido alterado
  $request->setContentType(/*content-type:string*/);
  /***/
  $request->status(/*status-code:int*/);
  # setStatus somente se o mesmo já não tenha sido alterado
  $request->setStatus(/*content-type:string*/);
  /***/
  $request->send(/*content:mixed*/);
  # sendStatus envia somente o status sem nenhum content-type
  $request->sendStatus(/*content:mixed*/);
  /***/
  ```

## Middlewares

- Altera ou evita a execução de uma rota de acordo com o contexto
- Podem ser gerais ou específicos para tais métodos
- configuração:

  ```php
  <?php

  require __DIR__ . '/pkit/load.php';

  use Pkit\Middlewares;
  /***/
  # namespace onde se localiza os middlewares a serem usados
  # padrão 'App\Middlewares'
  # pode ser configurado pelo .env 'MIDDLEWARES_NAMESPACE'
  Middlewares::config('\App\Middlewares');
  # Obs.: o framework já possui middlewares padrões
  /***/
  ```

- exemplo:

  ```php
  <?php
  //.../app/middlewares/api.php
  namespace App\Middlewares;

  use Pkit\Abstracts\Middleware;
  use Pkit\Http\ContentType;

  class Api implements Middleware
  {
    public function handle($request, $response, $next)
    {
      $response->contentType(/*content-type:string*/);
      return $next($request, $response);
    }
  }

  ```

- uso:

  ```php
  <?php
  //.../*
  /*rota*/
  public $middlewares = [
      "pkit/auth", # middlewares sem chaves são usados em todos o métodos
      # podem ser específicos para cada métodos
      "get" => [
        "pkit/api",
      ],
      # não precisam ser arrays
      "post" => "pkit/onlycode"
    ];
  /*rota*/
  ```

- Referências:
  - [Middleware Abstraction](../abstracts/Abstracts.md)
  - [Framework Middlewares](../middlewares/Middlewares.md)

## ContentType

- São os content-types suportado no envio das responses
- São apenas constantes
- exemplo/uso:

  ```php
  <?php

  use Pkit\Http\ContentType;
  /***/
  $response->contentType(ContentType::JSON);
  /***/
  ```

## Status

- São os status suportados no envio das responses
- São apenas constantes
- exemplo/uso:

  ```php
  <?php

  use Pkit\Http\Status;
  /***/
  $response->status(Status::UNAUTHORIZED);
  /***/
  ```
