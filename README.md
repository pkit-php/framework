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

- mapeie os middlewares as serem usados

```php
use Pkit\Http\Middleware\Queue;
use Pkit\Http\Middleware\Api;
# ...

Queue::setMap([
  "api" => API::class,
  # ...
]);
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

new PKit\Http\Route

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
    $params = $request->getRouter()->getParams();
    $response->json()->send($params);
  }
}

(new fileByRepo())->run();
```

## Middlewares

### configuração

- inicie com namespace onde se encontra os middlewares adicionais no `index.php`

```php
/* ... */
use App\Middlewares as MiddlewaresNamespace;

Middlewares::init(MiddlewaresNamespace::class);
/* ... */
```

### exemplo de criação

```php
<?php

namespace App\Middlewares;

use Pkit\Abstracts\Middleware;

class Teste implements Middleware{
    public function handle($request, $reponse, $next)
    {
        echo 'teste';
        /* ... */
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
      'pkit/api',# middlewares do framework inicião com 'pkit/'
      # chaves nomeados são pra métodos específicos
      'post' => [
        'pkit/auth',
      ],
    ];
    
    function get($request, $response)
    {
        $response->send("...");
    }

    function post($request, $response)
    {
        $response->send("...");
    }
}
(new Home)->run();
```

### lista de middlewares do framework

- `pkit/api` : converte o content-type para application/json

- `pkit/auth` : autentica o usuario com base na sessão

## Session

### exemplo de uso de sessão

- login

  ```php
  <?php 

  use Pkit\Abstracts\Route;
  use Pkit\Utils\Session;

  class Login extends Route {
      
      function get($request, $response)
      {
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

  use Pkit\Abstracts\Route;
  use Pkit\Utils\Session;

  class Logout extends Route {

      public $middlewares = [
        'pkit/auth',
      ];
      
      function get($request, $response)
      {
          Session::logout();
          $response->send("dislogged");
      }
  }
  (new Logout)->run();
  ```
