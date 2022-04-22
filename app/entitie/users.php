<?php namespace App\Entitie;

use Pkit\Database\Table;

class Users extends Table
{
  public 
    $id,
    $name;
  protected
    $email,
    $password,
    $created,
    $updated;
}
