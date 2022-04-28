# Auth

Classe de autenticação e criação de tokens genéricos

## Session

- Funciona a partir da sessão do php
- É regerado sempre que a sessão é desligada
- Pode durar uma sessão ou um tempo pre-determinado
- configuração :

  ```php
  <?php
   // .../index.php
  require __DIR__ . '/pkit/load.php';

  use Pkit\Auth\Session;
  /***/
  Session::init(/*tempo em segundos*/);//opcional
  /***
  ```

- uso :

  ```php
  <?php
  // .../*
  use Pkit\Auth\Session;
  /***/
  $logged = Session::isLogged(); //: boolean
  /***/
  Session::login(/*payload: array*/);
  /***/
  Session::logout()
  /***/

  ```

## Jwt

- É enviado um token criptografado para o cliente e assim validado no retorno
- Por padrão ele é enviado pelo cabeçalho 'Authorization' com o "Bearer \<token>"
- Pode ser valido pra sempre ou um tempo pre-determinado
- configuração :

  ```php
  <?php
   // .../index.php
  require __DIR__ . '/pkit/load.php';

  use Pkit\Auth\Jwt;
  /***/
  Jwt::init(/*chave de criptografia*/,/*tempo em segundos(opcional)*/);
  /***
  ```

- uso :

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
  Jwt::setBearer(/*response:Response*/,/*token:string*/)
  /***/
  $token = Jwt::getBearer(/*request:Request*/)//:string;
  /***/

  ```
