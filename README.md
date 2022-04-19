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

## rotas

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
  # middlewares sem chaves são utilizados em todos o métodos
  public $middlewares = [
    'api',
    'post' => [
      'requireAuth',
    ],
  ];

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
