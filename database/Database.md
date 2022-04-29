# Database

Classe de autenticação e criação de tokens genéricos

## Database

- Conecta ao banco de dados desejado
- É usado através de query's construídos a mão com possíveis parâmetros
- configuração:

  ```php
  <?php

  require __DIR__ . '/pkit/load.php';

  use Pkit\Database\Database;
  /***/
  Database::init(
    getenv('DB'),
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
  );
  ```

- uso:

  ```php
  <?php
  // .../*
  use Pkit\Database\Database;
  /***/
  $statement = (new Database)->execute(/*query:string*/,/*parâmetros:array(opcionais)*/);
  $result = $statement->fetchAll();
  /***/

  ```

## Table

- É estendido a um modelo, feito a manipulação através dos seus parâmetros
- Os parâmetros protegidos são reconhecidos, porem não é enviado em uma requisição
- São feito os métodos básicos de CRUD
- exemplo de Modelo:

  ```php
  <?php

  namespace App\Entities;

  use Pkit\Database\Table;

  class Users extends Table
  {
    public
      $id,
      $name,
      $email;
    protected
      $password;
  }

  ```

- uso:

  ```php
  <?php
  // .../*
  use App\Entities\Users;
  /***/
  $user = new Users;
  /***/

  $user->name = "name";
  //...
  $return = $user->insert(/*parâmetro a ser retornado:string(opcional)*/)//:Modelo;
  /***/
  $payload = $user->select(/*where:array(opcional)*/,/*orderBy:string(opcional)*/, /*limit:array(opcional)*/);//:array<Modelo>
  /***/

  $user->name = "new name";
  //...
  $user->update(/*where:array(opcional)*/);
  /***/
  $user->delete(/*where:array(opcional)*/);
  /***/

  ```

  - where

    `\<field>:\<contition> => <value>`

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
