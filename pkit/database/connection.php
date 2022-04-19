<?php

namespace Pkit\Database;

use \PDO;

class Connection
{

  private PDO $pdo;
  private static $debug = false;

  private static string
    $db,
    $host,
    $dbname,
    $user,
    $pass;

  public static function init(array $set)
  {
    self::$db = $set[0];
    self::$host = $set[1];
    self::$dbname = $set[2];
    self::$user = $set[3];
    self::$pass = $set[4];
  }


  private function connect()
  {
    $db = self::$db;
    $host = self::$host;
    $dbname = self::$dbname;
    $user = self::$user;
    $pass = self::$pass;
    $this->pdo = new PDO("$db:host=$host;dbname=$dbname", $user, $pass);
    // throw exceptions, when SQL error is caused
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // prevent emulation of prepared statements
    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  }

  public function query($query, $values = [])
  {
    $this->connect();
    try {
      $stmt = $this->pdo->prepare($query);
      foreach ($values as $key => $value) {
        $stmt->bindValue($key, $value);
      }
      $stmt->execute();
    } catch (\Throwable $th) {
      if (Self::$debug) {
        echo $th;
      } else {
        throw $th;
      }
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
