# PKIT

<p align="center">
<a href="https://packagist.org/packages/kauaug/pkit"><img src="https://img.shields.io/packagist/dt/kauaug/pkit" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/kauaug/pkit"><img src="https://img.shields.io/packagist/v/kauaug/pkit" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/kauaug/pkit"><img src="https://img.shields.io/packagist/l/kauaug/pkit" alt="License"></a>
</p>

<div align="center"><a href="https://packagist.org/packages/kauaug/pkit"><img src="icon.png"></a></div>

Pkit é um framework php para aplicações web que visa facilitar o desenvolvimento de tais projetos.

## Instalação

- Instale o Pkit através do [composer](https://getcomposer.org/download/)
  ```sh
  composer require kauaug/pkit
  ```

## Como iniciar

- adicione o `.htaccess` para que só considere o `index.php`

  ```apache
  RewriteEngine On
  RewriteRule . index.php [L,QSA]
  ```

- inicialize o pkit

  ```php
  require __DIR__ . '/vendor/autoload.php';
  ```

- inicie o roteador com o path das rotas

  ```php
  //.../index.php
  use Pkit\Http\Router;
  /***/
  # padrão '[root]/routes'
  # pode ser configurado pelo .env 'ROUTE_PATH', 'PUBLIC_PATH', 'DOMAIN', 'SUB_DOMAIN' respectivamente
  Router::config(__DIR__ . "/routes", __DIR__. "/public", "pkit.com", true);
  Router::run();
  ```

## Rotas

as rotas são adicionadas dentro da pasta `routes` e os caminhos são com base nos diretórios e nome dos arquivos, é suportado desde arquivos `.php` até arquivos estáticos como `.css` e `.js`

```files
.htaccess
index.php
pkit/
routes/
├ public/
│ └ style.css
├ home.php
└ index.php
```

- exemplo de rota

  ```php
  <?php
  //.../routes/*
  use Pkit\Abstracts\Route;

  # classe abstrata para adição de rotas por método
  class index extends Route
  {
    public function get($request, $response)
    {
      $response->ok()->send('GET index.php');
    }

    public function post($request, $response)
    {
      $response->ok()->send('POST index.php');
    }

  }

  # função que inicia a rota
  Index::run();

  ```

- rotas avançadas

  - `(...)` 'somente letras e números'
  - `[...]` 'qualquer coisa entre `/`'
  - `{...}` 'qualquer coisa'

  ```files
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

  - exemplo de como pegar esse parâmetros

    ```php
    <?php
    //.../routes/(id)/[repo]/{file}.php

    use PKit\Abstracts\Route;
    use PKit\Http\Router;

    class FileByRepo extends Route
    {
      public function get($request, $response)
      {
        /**
         * $params = [
         *  'id' => '[a-zA-Z0-9]',
         *  'repo' => '[^\/]'
         *  'file' => '.*'
         * ]
         */
        $params = Router::getParams();
        $response->json()->send($params);
      }
    }

    FileByRepo::run();
    ```

- rota especial

  `Rota que intercepta erros de rotas ou chamadas intencionais a mesma, ainda funcionando como um rota comum. Essa rota deve ser chamada de '*.php' e estar no pasta routes.`

  - exemplo de uso em um middleware

    ```php
    <?php
    //.../app/middlewares/maintenance.php
    namespace Pkit\Middlewares;

    use Pkit\Abstracts\Middleware;
    use Pkit\Http\Router;

    class Maintenance implements Middleware
    {
      public function handle($request, $response, $next)
      {
        $response->serviceUnavailable();
        Router::runEspecialRoute();# <--
      }
    }
    ```

## Middlewares

- inicie com namespace onde se encontra os middlewares adicionais no `index.php`

  ```php
  //.../index.php
  /***/
  use App\Middlewares as MiddlewaresNamespace;

  Middlewares::config(MiddlewaresNamespace::class);
  /***/
  ```

- exemplo de criação

  ```php
  <?php
  //.../app/middlewares/teste.php
  namespace App\Middlewares;

  use Pkit\Abstracts\Middleware;

  class Teste extends Middleware{
      public function handle($request, $response, $next)
      {
          echo 'teste';
          /***/
          $next($response, $request)
      }
  }
  ```

- exemplo de uso

  ```php
  <?php
  //.../routes/home.php
  use Pkit\Abstracts\Route;

  class Home extends Route {

      public $middlewares = [
        'Teste', # middlewares adicionados
        'Pkit/Api',# middlewares do framework iniciam com 'pkit/'
        # chaves nomeados são pra métodos específicos
        'post' => [
          'Pkit/Auth',
        ],
      ];

      function get($request, $response)
      {
        /***/
        $response->send(/***/);
      }

      function post($request, $response)
      {
        /***/
        $response->send(/***/);
      }
  }
  Home::run();
  ```

- lista de middlewares do framework
  - `Pkit/Api` : converte o content-type para application/json
  - `Pkit/Auth` : autentica o usuário com base na sessão
  - `Pkit/Maintenance` : indica um rota em manutenção
  - `Pkit/Jwt` : autentica o usuário com base no bearer token(jwt)
  - `Pkit/OnlyCode` : converte o content-type para nulo

## Session

- configuração:

  ```php
  <?php
   // .../index.php
  require __DIR__ . '/pkit/load.php';

  use Pkit\Auth\Session;
  /***/
  # pode ser configurado pelo .env 'SESSION_EXPIRES' e 'SESSION_PATH' respectivamente
  Session::config(/*tempo em segundos*/, /*caminho para a sessão(opcional)*/);//opcional
  /***
  ```

- login

  ```php
  <?php
  //.../routes/login.php
  use Pkit\Abstracts\Route;
  use Pkit\Auth\Session;

  class Login extends Route {

      function get($request, $response)
      {
          /***/
          Session::login([
            'id' => '1234',
            'name' => 'user...'
          ]);
          $response->send("logged");
      }
  }
  Login::run();
  ```

- logout

  ```php
  <?php
  //.../routes/logout.php
  use Pkit\Abstracts\Route;
  use Pkit\Auth\Session;

  class Logout extends Route {

      public $middlewares = [
        'Pkit/Auth',
      ];

      function get($request, $response)
      {
          /***/
          Session::logout();
          $response->send("dislogged");
      }
  }
  Logout::run();
  ```

## Jwt

- configuração do Jwt

  ```php
  //.../routes/logout.php
  use Pkit\Auth\Jwt;
  /***/
  # pode ser configurado pelo .env 'JWT_KEY', 'JWT_EXPIRES' e 'JWT_ALG' respectivamente
  Jwt::config(/*chave para criptografia*/, /*tempo de expiração em segundos #opcional*/, /*algoritmo de criptografia*/));
  /***/
  ```

- token

  ```php
  <?php
  //.../routes/login.php
  use Pkit\Abstracts\Route;
  use Pkit\Auth\Jwt;

  class Login extends Route {

      public $middlewares = [
        'get' => 'Pkit/jwt'
      ]

      function get($request, $response)
      {
        /***/
        # pega o token enviado pelo header 'Authorization'
        $token = Jwt::getBearer();
        $response->send(Jwt::getPayload($token));
      }

      function post($request, $response)
      {
        /***/
        $token = Jwt::tokenize(/*payload*/);
        # envia o token pelo header 'Authorization'
        Jwt::setBearer($token);
      }
  }
  Login::run();
  ```

## Database

- configuração do banco de dados

  ```php
  //.../index.php
  use Pkit\Database\Database;
  /***/
  # pode ser configurado pelo .env 'DB_DRIVER', 'DB_HOST', 'DB_PORT', 'DB_DBNAME', 'DB_USER' e 'DB_PASS' respectivamente
  Database::config(
    [
      "driver" => 'mysql',
      "host" => 'localhost',
      "port" => '3503',
      "dbname" => 'database',
    ],
    'root',
    '',
  );
  /***/
  ```

- exemplo de uso do database

  ```php
  //.../*
  /***/
  use Pkit\Database\Database;
  /***/
  (new Database)->execute('SELECT * FROM Table WHERE id=?', [$id]);
  /***/
  ```

- exemplo de uso de tabela

  ```php
  <?php
  // app/entities/user.php

  namespace App\Entities;

  use Pkit\Database\Table;

  // o nome da classe deve ser o mesmo da tabela do banco de dados
  class User extends Table
  {
    protected
      $_table = 'Users'; // o atributo protected _table sobrepõe o nome da classe
    # os atributos devem ser os mesmos da tabela do banco de dados
    // podem ser visto pelo cliente
    public
      $id,
      $name,
      $email;
    // só é possível usar dentro da aplicação
    protected
      $password;
  }

  ```

- select

  ```php
  /***/
  (new Users)->select(where: ['id:>', $id], orderBy: ['name'], limit: [10, 40]);
  /***/
  ```

- insert

  ```php
  /***/
  $user = new Users;

  $user->name = 'name';
  $user->email = 'email@email.com';
  $user->password = password_hash('1234', PASSWORD_DEFAULT);

  $id = $user->insert(return: 'id');
  /***/
  ```

- update

  ```php
  /***/
  $user = new Users;

  $user->name = 'name-name';
  $user->email = 'email@email.com.br';
  $user->password = password_hash('4321', PASSWORD_DEFAULT);

  $user->update(where: ['id' => $id]);
  /***/
  ```

- where

  `'<field>:<condition>' => <value>`

  - condition
    | up | lo | di | eq |
    |----|----|----|----|
    | > | < | <> | = |

  - exemplo

    ```php
    [
      "status = 1", # valores sem chaves são escritos por extenso
      "id" => $id, # a condição padrão é '='
    ]
    ```

- order

  - exemplo

    ```php
    [
      "name",
      "email",
      /***/
    ]
    ```

- limit

  - exemplo

    ```php
    [
      10, # item inicial
      5, # quantidade de items
    ]
    ```

## View

- configuração do path de views

  ```php
  // index.php
  /***/
  # padrão '[root]/view'
  # pode ser configurado pelo .env 'PATH_VIEW'
  View::config(__DIR__ . '/app/view');
  /***/
  ```

- é considerado a partir da pasta configurada
- o argumentos são pegos a partir de `View::getArgs()` e renderizados a partir de `View::render(<file>)`
- obs.: somente os arquivos `.phtml` são reconhecidos

  ```php
  <?php
  //.../view/home/index.phtml
  use Pkit\Utils\View;
  ?>
  <main>
    <?php
    // $_ARGS# 'superglobal' que recebe os argumentos do render
    foreach ($_ARGS as $key => $value) {
      View::render('componentes/home/p', $key . ' : ' . $value);
    }
    ?>
  </main>
  ```

### layout

- deve estar no arquivo `__layout.php` no caminho para a view
- para dar reset o layout deve estar no arquivo `__layout.reset.php`
- para adicionar o arquivo ao layout deve se usar `View::slot(/*args*/)`

  ```phtml
  <?php
  //.../view/home/__layout.phtml
  use Pkit\Utils\View;
  ?>
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title><?= $_ARGS['title'] ?></title>
      <meta name="description" content="<?= $_ARGS['description'] ?>">
    </head>
    <body>
      <main>
        <?= View::render("componentes/header") ?>
        <?= View::slot($_ARGS) # deve-se passar os argumentos ao slot ?>
        <?= View::render("componentes/footer") ?>
      </main>
    </body>
  </html>
  ```

- exemplo de uso

  ```php
  <?php
  //.../routes/index.php
  use Pkit\Abstracts\Route;
  use Pkit\Utils\View;

  class Index extends Route
  {
    public function get($request, $response)
    {
      # o método layout tem os mesmo parâmetros do render, porem envolto com o __layout
      $response->render(View::layout('home/index', [
        'title' => 'Home',
        'description' => 'tela inicial',
      ]));
    }
  }

  Index::run();
  ```

- lista de viewers do framework

  `obs.:é usado da mesma forma que os middlewares do framework`

  - `pkit/code` : pagina que mostra um svg animado com base no código
    - argumentos
      - lang
      - title
      - code
      - color
      - message
  - `pkit/redirect` : pagina que mostra uma tela de carregamento animado, ainda redireciona para o site pedido
    - argumentos
      - lang
      - title
      - site

## Variáveis de ambiente especiais

```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_DBNAME=database
DB_USER=root
DB_PASS=
DB_CHARSET=utf8
DB_DIALECT=3
ROUTE_PATH=/var/www/pkit/routes
PUBLIC_PATH=/var/www/pkit/public
VIEW_PATH=/var/www/pkit/view
SESSION_TIME=0 # tempo da sessão do PHP
SESSION_PATH=/var/lib/php/sessions # path da sessão
JWT_KEY=abcde # chave do JWT
JWT_EXPIRES=0 # tempo de expiração do JWT
JWT_ALG=0 # algoritmo de criptografia do JWT
DOMAIN=pkit.com # domínio a ser desconsidera ao procura o subdomínio
SUB_DOMAIN=true # se true o subdomínio declarado será considerado com parte da URI
PKIT_DEBUG=true # se true, caso aja erro, mostra uma pagina com o código de erro e a mensagem do erro
PKIT_CLEAR=false # se false, mantêm o conteúdo renderizado mesmo que tenha sido ocasionado um erro
```

_para mais informações acesse as documentações nas pastas no framework_
