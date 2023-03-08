# Http

Comportamento geral em relação a comportamentos relacionados ao Protocolo HTTP

## Request

- Armazena as informações relacionado a requisição
- Suporta conteúdos em `json/xml/form` desde que esteja explicito na requisição
- uso:

  ```php
  <?php
  //.../*
  use use Pkit\Http\Request;
  /***/
  $request = Request::getInstance();
  /***/
  $postVars = $request->postVars;//:array
  /***/
  $queryParams = $request->queryParams;//:array
  /***/
  $headers = $request->headers;//:array
  /***/
  $uri = $request->uri;//:array
  /***/
  ```

## Response

- Configura as informações a serem enviadas
- Suporta conteúdos em `json/html/form/xml` desde que esteja explicito
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
  use Pkit\Middlewares\Auth;
  use Pkit\Middlewares\Maintenance;

  /*rota*/
  public $middlewares = [
      Maintenance::class,
      # podem ser específicos para cada métodos e não precisam ser arrays
      "GET" => Auth::class, # middlewares sem chaves são usados em todos o métodos
      ,
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
  use PKit\Http\Response;
  /***/
  return (new Response())
    ->contentType(ContentType::JSON);
  /***/
  ```

## Status

- São os status suportados no envio das responses
- São apenas constantes
- exemplo/uso:

  ```php
  <?php

  use Pkit\Http\Status;
  use PKit\Http\Response;
  /***/
  return (new Response())
    ->status(Status::UNAUTHORIZED);
  /***/
  ```
