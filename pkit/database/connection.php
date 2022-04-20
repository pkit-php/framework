<?php

namespace Pkit\Database;

use \PDO;

class Connection
{

  private PDO $pdo;

  private static string
    $driver,
    $host,
    $dbname,
    $user,
    $pass;

  public static function init(array $set)
  {
    self::$driver = $set['driver'];
    self::$host = $set['host'];
    self::$dbname = $set['name'];
    self::$user = $set['user'];
    self::$pass = $set['pass'];
  }


  private function connect()
  {
    $driver = self::$driver;
    $host = self::$host;
    $dbname = self::$dbname;
    $user = self::$user;
    $pass = self::$pass;
    $this->pdo = new PDO("$driver:host=$host;dbname=$dbname", $user, $pass);
    // throw exceptions, when SQL error is caused
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // prevent emulation of prepared statements
    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  }

  public function execute($query, $values = [])
  {
    $this->connect();

    $stmt = $this->pdo->prepare($query);
    foreach ($values as $key => $value) {
      $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
