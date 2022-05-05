# PKIT

<div align="center"><img src="icon.png"></div>

`framework ainda em desenvolvimento`

## como iniciar

- adicione o `.htaccess` para que só considere o `index.php`

  ```apache
  RewriteEngine On
  RewriteRule . index.php [L,QSA]
  ```

- adicione a pasta `pkit`

  ```files
  .htaccess
  index.php
  pkit/
  ```

- inicialize o pkit

  ```php
  require __DIR__ . '/pkit/load.php';
  ```

- inicie o roteador com o path das rotas

  ```php
  //.../index.php
  use Pkit\Http\Router;
  /***/

  Router::init(__DIR__ . "/routes");
  Router::run();
  ```

## Rotas

as rotas são adicionadas dentro da pasta `routes` e os caminhos são com base nos diretórios e nome dos arquivos, é suportado desde arquivos `.php` até arquivos estáticos como `.css` e `.js`

- exemplo

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

### exemplo de rota

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

### rotas avançadas

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

#### exemplo de como pegar esse parâmetros

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

### rota especial

Rota que intercepta erros de rotas ou chamadas intencionais a mesma, ainda funcionando como um rota comum. Essa rota deve ser chamada de `*.php` e estar no pasta routes.

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

### configuração

- inicie com namespace onde se encontra os middlewares adicionais no `index.php`

  ```php
  //.../index.php
  /***/
  use App\Middlewares as MiddlewaresNamespace;

  Middlewares::init(MiddlewaresNamespace::class);
  /***/
  ```

### exemplo de criação

```php
<?php
//.../app/middlewares/teste.php
namespace App\Middlewares;

use Pkit\Abstracts\Middleware;

class Teste implements Middleware{
    public function handle($request, $response, $next)
    {
        echo 'teste';
        /***/
        $next($response, $request)
    }
}
```

### exemplo de uso

```php
<?php
//.../routes/home.php
use Pkit\Abstracts\Route;

class Home extends Route {

    public $middlewares = [
      'teste', # middlewares adicionados
      'pkit/api',# middlewares do framework iniciam com 'pkit/'
      # chaves nomeados são pra métodos específicos
      'post' => [
        'pkit/auth',
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

### lista de middlewares do framework

- `pkit/api` : converte o content-type para application/json
- `pkit/auth` : autentica o usuário com base na sessão
- `pkit/maintenance` : indica um rota em manutenção
- `pkit/jwt` : autentica o usuário com base no bearer token(jwt)
- `pkit/onlycode` : converte o content-type para nulo

## Session

### exemplo de uso de sessão

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
        'pkit/auth',
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

### configuração do Jwt

```php
//.../routes/logout.php
use Pkit\Auth\Jwt;
/***/
Jwt::init(/*chave para criptografia*/, /*tempo de expiração em segundos #opcional*/));
/***/
```

### exemplo de uso de Jwt

- token

  ```php
  <?php
  //.../routes/login.php
  use Pkit\Abstracts\Route;
  use Pkit\Auth\Jwt;

  class Login extends Route {

      public $middlewares = [
        'get' => 'pkit/jwt'
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

configuração do banco de dados

```php
//.../index.php
use Pkit\Database\Database;
/***/
Database::init(
  [
    "driver" => getenv("DB"),
    "host" => getenv("DB_HOST"),
    "port" => getenv("DB_HOST"),
    "dbname" => getenv("DB_NAME"),
  ],
  getenv("DB_USER"),
  getenv("DB_PASS"),
);
/***/
```

### exemplo de uso do database

```php
//.../*
/***/
use Pkit\Database\Database;
/***/
(new Database)->execute('SELECT * FROM Table WHERE id=?', [$id]);
/***/
```

### exemplo de uso de tabela

```php
<?php
// app/entities/user.php

namespace App\Entities;

use Pkit\Database\Table;

// o nome da classe deve ser o mesmo da tabela do banco de dados
class Users extends Table
{
  // os atributos devem ser os mesmos da tabela do banco de dados

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
  (new Users)->select(where: ['id:>', $id], orderBy: 'name ASC', limit: [10, 40]);
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

- limit

  - exemplo

    ```php
    [
      10, # item inicial
      5, # quantidade de items
    ]
    ```

## View

configuração do path de views

```php
// index.php
/***/
View::init(__DIR__ . '/app/view');
/***/
```

### render

- é considerado a partir da pasta configurada
- o argumentos são pegos a partir de `View::getArgs()` e renderizados a partir de `View::render(<file>)`
- obs.: somentes os arquivos `.phtml` são reconhecidos

  ```php
  <?php
  //.../app/view/home/index.phtml
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
  //.../app/view/home/__layout.phtml
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
      View::layout('home/index', [
        'title' => 'Home',
        'description' => 'tela inicial',
      ], $response, 200);
    }
  }

  Index::run();
  ```

### lista de viewers do framework

obs.:é usado da mesma forma que os middlewares do framework

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

```sh
PKIT_DEBUG=true  # se true, caso aja erro, mostra uma pagina com o código de erro e a mensagem do erro
PKIT_CLEAR=false # se false, mantém o conteúdo renderizado mesmo que tenha sido ocasionado um erro
```

_para mais informações acesse as documentações nas pastas no framework_
