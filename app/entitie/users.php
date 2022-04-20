<?php namespace App\Entitie;

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
