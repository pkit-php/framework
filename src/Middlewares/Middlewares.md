# Middlewares do framework

## [`Pkit\Middlewares\Auth`](./Auth.php)

O middleware `Auth` autentica o usuário com base na sessão ou jwt de forma sequencial

```php
<?php

use Pkit\Middlewares\Auth;
use Pkit\Middlewares;

class WhoAmI extends Route
{
    // Nesse caso a sessão tem preferência sobre o jwt
    #[Middlewares([Auth::class => ['Session', 'JWT']])]
    public function GET($request): Response
    {
        return new Response([
          "name": "I don't know"
        ]);
    }
}

return new WhoAmI;
```

## [`Pkit\Middlewares\Maintenance`](./Middlewares)

O middleware `Maintenance` indica um rota em manutenção

```php
<?php

use Pkit\Middlewares\Auth;
use Pkit\Middlewares;

class InMaintenance extends Route
{
    // Será retornado o código 503
    #[Middlewares([Auth::class => ['Session', 'JWT']])]
    public function GET($request): Response
    {
        return new Response("this is not returned");
    }
}

return new InMaintenance;
```

## Referência

- [Middleware Abstraction](../abstracts/Abstracts.md)
- [Http Middlewares](../http/Http.md)
