# Pkit

![https://packagist.org/packages/pkit/pkit](https://img.shields.io/packagist/dt/pkit/pkit)
![https://packagist.org/packages/pkit/pkit](https://img.shields.io/packagist/v/pkit/pkit)
![https://packagist.org/packages/pkit/pkit](https://img.shields.io/packagist/l/pkit/pkit)

![https://github.com/pkit-php/pkit/raw/master/icon.png](https://github.com/pkit-php/pkit/raw/master/icon.png)

`Esse projeto está parado para fins de estudo`

Pkit é um framework php para aplicações web que visa facilitar o desenvolvimento de tais projetos.

## Instalação

Instale o Pkit através do [composer](https://getcomposer.org/download/)

`composer require pkit/pkit`

## Como iniciar

- adicione o `.htaccess` para que só considere o `index.php`

    ```php
    RewriteEngine On
    RewriteRule . index.php [L,QSA]
    ```

- inicie o roteador

    ```php
    <?php
    //.../index.php
    require __DIR__ . '/vendor/autoload.php'
    
    use Pkit\Router;
    /***/
    // o padrão de rotas são '[root]/routes' e '[root]/public'
    // pode ser configurado pelo .env 'ROUTE_PATH', 'PUBLIC_PATH', 'DOMAIN', 'SUB_DOMAIN' respectivamente
    Router::config(__DIR__ . "/routes", __DIR__. "/public", "pkit.com", true);// (opcional)
    Router::run();
    ```

## Rotas

as rotas são adicionadas dentro da pasta `routes` e os caminhos são com base nos diretórios e nome dos arquivos, é suportado desde arquivos `.php` até arquivos estáticos como `.css` e `.js`

```php
.htaccess
index.php
pkit/
routes/
├ public/
│ └ style.css
├ home.php
└ index.php
```

O retorno padrão para a instancia de rotas é `\Pkit\Abstracts\Route`, sendo os métodos da classe os mesmos do HTTP, porem podendo ser retornado qualquer objeto que possa ser chamado ou qualquer string.

```php
<?php
//.../routes/**/*.php
use Pkit\Abstracts\Route;
use Pkit\Http\Response;

// classe abstrata para adição de rotas por método
class index extends Route
{
  public function GET($request)
  {
    return new Response('GET');
  }

  public function POST($request)
  {
    return new Response('POST');
  }

}

// A rota funciona com base em objetos que podem ser chamados ou string retornadas
return new Index;
```

### Rotas avançadas

As rotas avançadas são aquelas que recebem variáveis em meio as rotas

```php
.htaccess
index.php
pkit/
routes/
├ [id]/
│ ├ [repo]/
│ │ ├ [...file].php
│ │ └ index.php
│ └ index.php
├ home.php
└ index.php
```

- `[abc]` : qualquer coisa entre barras
- `[abc=int]` / `[abc=integer]` : qualquer numero sem casas decimais entre barras
- `[abc=float]` : qualquer numero flutuante entre barras
- `[abc=word]` : qualquer palavra entre barras
- `[...abc]` : qualquer coisa depois da barra

Esses parâmetros são pegos através da instancia estática do Router

```php
<?php
//.../routes/[id=int]/[repo=word]/[...file].php

use PKit\Abstracts\Route;
use PKit\Router;

class FileByRepo extends Route
{
  public function GET($request)
  {
    /**
     * $params = [
     *  'id' => '[1-9]*',
     *  'repo' => '[\w]*'
     *  'file' => '.*'
     * ]
     */
    $params = Router::getParams();
    /***/
    return new Response(/***/);
  }
}

return new FileByRepo;
```

### Rota especial

As rota interceptam erros das rotas, ainda funcionando como um rota comum, contudo qualquer método HTTP é pego pelo método `ALL`. Essa rota deve ser chamada de '*.php' e estar no pasta `routes`.

```php
<?php
//.../routes/*.php
namespace Pkit\Middlewares;

use Pkit\Abstracts\Route;
use Pkit\Http\Response;

class EspecialRoute extends Route
{
 // esse método intercepta erros em qualquer um dos métodos HTTP
  public function ALL($request, $err)
  {
  /***/
  return new Response(/***/);
  }
}
```

## Middlewares

Os Middlewares são intermediários das rotas, podendo conter parâmetros para uma configuração dinâmica.

```php
<?php
//.../app/middlewares/teste.php
namespace App\Middlewares;

use Pkit\Abstracts\Middleware;

class Teste extends Middleware {
  // Os middlewares são baseados em closures
    public function __invoke($request, $next, $params = null)
    {
        echo 'teste';
        /***/
        return $next($request);
    }
}
```

Eles são tanto definidos em um atributo publico chamado `$middlewares` da classe `\Pkit\Abstracts\Route` quanto por atributos definidos acima do método.

```php
<?php
//.../routes/home.php
use App\Middlewares\Teste;
/***/
use Pkit\Abstracts\Route;
use Pkit\Middlewares\Auth;
use Pkit\Middlewares;
use Pkit\Http\Response;

class Home extends Route {

    public $middlewares = [
      Teste::class, // middlewares adicionados
      /***/
      // chaves nomeados são pra métodos específicos
      'POST' => [
      // os valores são passados como parâmetros para o Middleware
        Auth::class => "jwt",
      ],
    ];

    function GET($request)
    {
      /***/
      return new Response(/***/);
    }

    // os atributos podem ser usado para definir os middlewares
    #[Middlewares([Auth::class => "session",])]
    function POST($request)
    {
      /***/
      return new Response(/***/);
    }
}

return new Home;
```

### middlewares do framework

- `Pkit/Middlewares/Auth`  : autentica o usuário com base na sessão
  - Os possíveis parâmetros são “jwt”, “session” ou a sequência configuradas por array
- `Pkit/Middlewares/Maintenance` : indica um rota em manutenção

## Session

```php
<?php
 // .../index.php
require __DIR__ . '/pkit/load.php';

use Pkit\Auth\Session;
/***/
// pode ser configurado pelo .env 'SESSION_EXPIRES' e 'SESSION_PATH' respectivamente
Session::config(/*tempo em segundos*/, /*caminho para a sessão(opcional)*/);// (opcional)
/***/
```

### login

```php
<?php
//.../routes/login.php
use Pkit\Abstracts\Route;
use Pkit\Auth\Session;
use Pkit\Http\Response;

class Login extends Route {

    function GET($request)
    {
        /***/
        Session::login([
          'id' => '1234',
          'name' => 'user...'
        ]);
        return new Response("logged");
    }

  /***/
}

return new Login;
```

### logout

```php
<?php
//.../routes/logout.php
use Pkit\Abstracts\Route;
use Pkit\Auth\Session;
use Pkit\Http\Response;
use Pkit\Middlewares\Auth;
use Pkit\Middlewares;

class Logout extends Route {

    #[Middlewares([
      Auth::class => 'session'
    ])]
    function GET($request)
    {
        /***/
        Session::logout();
        return new Response("logged out");
    }
  
  /***/
}

return new Logout;
```

## Jwt

```php
<?php
//.../routes/logout.php
use Pkit\Auth\Jwt;
/***/
// pode ser configurado pelo .env 'JWT_KEY', 'JWT_EXPIRES' e 'JWT_ALG' respectivamente
Jwt::config(/*chave para criptografia*/, /*tempo de expiração em segundos (opcional)*/, /*algoritmo de criptografia (opcional)*/));
/***/
```

### token

```php
<?php
//.../routes/login.php
use Pkit\Abstracts\Route;
use Pkit\Auth\Jwt;
use Pkit\Middlewares\Auth;

class Login extends Route {

    #[Middlewares([
      Auth::class => 'jwt'
    ])]
    function GET($request)
    {
      /***/
      // pega o token enviado pelo header 'Authorization'
      $token = Jwt::getBearer();
      return new Response(Jwt::getPayload($token));
    }

    function POST($request)
    {
      /***/
      $token = Jwt::tokenize(/*payload*/);
      // envia o token pelo header 'Authorization'
      Jwt::setBearer($token);
      return new Response(Jwt::getPayload($token));
    }

  /***/
}

return new Login;
```

## Variáveis de ambiente especiais

```php
MIDDLEWARES_NAMESPACE=\App\Middlewares # namespace onde se encontra os middlewares que não são do framework
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

***Para mais informações acesse as documentações nas pastas no framework***
