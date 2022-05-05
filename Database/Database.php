<?php

namespace Pkit\Database;

use \PDO;

class Database
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
    $config = self::$config;
    $driver = $config['driver'] ?? "mysql";
    $host = 'host=' . $config['host'] . ';' ?? "host=localhost;";
    $dbname = 'dbname=' . $config['dbname'] . ';' ?? "";
    $port = 'port=' . $config['port'] . ';' ?? "";

    $config = "$driver:" . $host . $port . $dbname;

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
