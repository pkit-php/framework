# PKIT

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
  use Pkit\Http\Router;

  $router = new Router(__DIR__ . '/routes');
  $router->init();
  $router->run();
  ```

## Rotas

as rotas são adicionadas dentro da pasta `routes` e os caminhos são com base nos diretórios e nome dos arquivos, é suportado desde arquivos `.php` até arquivos estáticos como `.css` e `.js`

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

new PKit\Http\Route

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
(new Index())->run();

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
# __DIR__./routes/(id)/[repo]/{file}.php

use PKit\Abstracts\Route;
use PKit\Http\Router;

class fileByRepo extends Route
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

(new fileByRepo())->run();
```

### rota especial

Rota que intercepta erros de rotas ou chamadas intencionais a mesma, ainda funcionando como um rota comum. Essa rota deve ser chamada de `*.php` e estar no pasta routes.

- exemplo de uso em um middleware

  ```php
  <?php

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
  /***/
  use App\Middlewares as MiddlewaresNamespace;

  Middlewares::init(MiddlewaresNamespace::class);
  /***/
  ```

### exemplo de criação

```php
<?php

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

use Pkit\Abstracts\Route;

class Home extends Route {

    public $middlewares = [
      'teste', # middlewares adicionais
      'pkit/api',# middlewares do framework iniciam com 'pkit/'
      # chaves nomeados são pra métodos específicos
      'post' => [
        'pkit/auth',
      ],
    ];

    function get($request, $response)
    {
      /***/
      $response->send("...");
    }

    function post($request, $response)
    {
      /***/
      $response->send("...");
    }
}
(new Home)->run();
```

### lista de middlewares do framework

- `pkit/api` : converte o content-type para application/json

- `pkit/auth` : autentica o usuário com base na sessão

- `pkit/maintenance` : indica um rota em manutenção

## Session

### exemplo de uso de sessão

- login

  ```php
  <?php
  //.../routes/login.php
  use Pkit\Abstracts\Route;
  use Pkit\Utils\Session;

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
  (new Login)->run();
  ```

- logout

  ```php
  <?php
  //.../routes/logout.php
  use Pkit\Abstracts\Route;
  use Pkit\Utils\Session;

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
  (new Logout)->run();
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
/***/
use Pkit\Database\Database;
/***/
(new Database)->execute('SELECT * FROM User WHERE id=?', [$id]);
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
  // view/home.php
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
  // view/__layout.php
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

- exemplo de uso do layout

  ```php
  <?php

  use Pkit\Abstracts\Route;
  use Pkit\Utils\View;

  class Index extends Route
  {
    public function get($request, $response)
    {
      View::layout('home', [
        'title' => 'Home',
        'description' => 'tela inicial',
      ]);
    }
  }

  (new Index)->run();
  ```

<strong>para mais informações acesse as documentações nas pastas no framework</strong>
