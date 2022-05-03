# PKIT

<div style="text-align: center;">
<svg width="257" height="210" viewBox="0 0 257 210" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M29.9245 0.149094L256.024 40.0165L226.123 209.591L0.0239707 169.724L29.9245 0.149094Z" fill="#FF2222"/>
<path d="M123.051 87.1585C122.006 93.1996 119.633 98.7216 115.93 103.724C112.321 108.633 107.432 112.597 101.26 115.618C95.0882 118.544 87.9195 120.007 79.7539 120.007H65.9388L59.957 153.706H31.8996L49.7025 53.8849H91.4326C101.972 53.8849 109.995 56.292 115.502 61.106C121.009 65.8256 123.763 72.1972 123.763 80.2206C123.763 82.2028 123.525 84.5155 123.051 87.1585ZM81.0357 98.0609C88.9165 98.0609 93.474 94.4267 94.7084 87.1585C94.8983 85.837 94.9932 84.893 94.9932 84.3267C94.9932 81.7781 94.1387 79.7958 92.4296 78.3799C90.8155 76.8696 88.2993 76.1145 84.8811 76.1145H73.7721L69.9267 98.0609H81.0357Z" fill="white"/>
<path d="M186.108 102.45L215.732 153.706H181.977L158.193 109.813L150.502 153.706H122.444L140.247 53.8849H168.162L160.471 97.0697L199.353 53.8849H231.541L186.108 102.45Z" fill="white"/>
</svg>
</div>

## como iniciar

- adicione o `.htaccess` para que só considere o `index.php`

  ```apache
  RewriteEngine On

  # RewriteCond %{REQUEST_URI} !\.(woff2|js|ico|json|png|jpg|gif)$ [NC]
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f

  RewriteRule ^(.*)$ index.php [L,QSA]
  ```

- adicione a pasta `pkit`

  ```
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

  $router = new Router(__DIR__ . '/routes');
  $router->init();
  $router->run();
  ```

## Rotas

as rotas são adicionadas dentro da pasta `routes` e os caminhos são com base nos diretórios e nome dos arquivos, é suportado desde arquivos `.php` até arquivos estáticos como `.css` e `.js`

- exemplo
  ```
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

```
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

### configuração

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
  getenv("DB"),
  getenv("DB_HOST"),
  getenv("DB_NAME"),
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

  `\<field>:\<condition> => <value>`

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

  ```php
  <?php
  //.../app/view/home.php
  use Pkit\Utils\View;
  ?>
  <main>
    <?php
    foreach (View::getArgs() as $key => $value) {
      View::render('componentes/home/p', $key . ' : ' . $value);
    }
    ?>
  </main>
  ```

### layout

- deve estar no arquivo `__layout.php` na pasta de views
- para adicionar o arquivo ao layout deve se usar `View::slot()`

  ```php
  <?php
  //.../app/view/__layout.php
  use Pkit\Utils\View;

  $_ARGS = View::getArgs()
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
      <?php
      View::render("componentes/header");
      View::slot();
      View::render("componentes/footer");
      ?>
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
      View::layout('home', [
        'title' => 'Home',
        'description' => 'tela inicial',
      ], $response, 200);
    }
  }

  Index::run();
  ```

<strong>para mais informações acesse as documentações nas pastas no framework</strong>
