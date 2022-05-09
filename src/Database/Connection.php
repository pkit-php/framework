<?php

namespace Pkit\Database;

use \PDO;

class Connection
{
  private PDO $pdo;

  private static array
    $config;
  private static string
    $user,
    $pass;

  public static function init($config, $user, $pass)
  {
    self::$config = $config;
    self::$user = $user;
    self::$pass = $pass;
  }

  private function connect()
  {
    $driver = self::$config['driver'];
    $config = strlen($driver) ? $driver . ":" : "mysql:";
    unset(self::$config['driver']);
    foreach (self::$config as $key => $value) {
      $config .= "$key=$value;";
    }
    $this->pdo = new PDO($config, self::$user, self::$pass);
    // throw exceptions, when SQL error is caused
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // prevent emulation of prepared statements
    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  }

  public function execute($query, $params = [])
  {
    $this->connect();
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    return $stmt;
  }
}
