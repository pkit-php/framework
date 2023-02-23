# Auth

Classes de autenticação e criação de tokens genéricos

## Session

A classe Session Funciona a partir da sessão do php e é regerado sempre que a sessão é desligada, além disso pode durar uma sessão ou um tempo pré-determinado.

- configuração:

  ```php
  <?php
   // .../index.php
  require __DIR__ . '/vendor/autoload.php';
  /***/
  use Pkit\Auth\Session;
  /***/
  # pode ser configurado pelo .env 'SESSION_EXPIRES' e 'SESSION_PATH' respectivamente
  Session::config(
    /*tempo em segundos*/, 
    /*caminho para a sessão(opcional)*/
  );//opcional
  /***
  ```

- uso:

  ```php
  <?php
  // .../*
  use Pkit\Auth\Session;
  /***/
  Session::login(/*payload: array*/);
  /***/
  $logged = Session::isLogged(); //: bool
  /***/
  Session::logout()//: bool
  /***/

  ```

## Jwt

O jwt é token criptografado que é enviado para o cliente e então validado no retorno, por padrão é enviado pelo cabeçalho 'Authorization' com o sufixo `Bearer`, além disso pode ser valido pra sempre ou como recomendado, ter um tempo de expiração.

- configuração da classe Jwt:

  ```php
  <?php
   // .../index.php
  require __DIR__ . '/vendor/autoload.php';

  use Pkit\Auth\Jwt;
  /***/
  # pode ser configurado pelo .env 'JWT_KEY', 'JWT_EXPIRES' e 'JWT_ALG' respectivamente
  Jwt::config(
    /*chave para criptografia*/, 
    /*tempo de expiração em segundos #opcional*/, 
    /*algoritmo de criptografia*/
  );
  /***
  ```

- uso:

  ```php
  <?php
  // .../*
  use Pkit\Auth\Jwt;
  /***/
  $token = Jwt::tokenize(/*payload:array*/)//:string;
  /***/
  $valid = Jwt::validate(/*token:string*/);//:boolean
  /***/
  $payload = Jwt::getPayload(/*token:string*/)//:object
  /***/
  Jwt::setBearer(/*response:Response*/,/*token:string*/)//:Response
  /***/
  $token = Jwt::getBearer(/*request:Request*/)//:string;
  /***/

  ```
