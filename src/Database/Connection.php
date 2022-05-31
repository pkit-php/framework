<?php

namespace Pkit\Database;

use \PDO;
use Pkit\Utils\Env;

class Connection
{
  private ?PDO $pdo = null;

  private static array
    $config = [];
  private static ?string
    $driver = null,
    $dbname = null,
    $host = null,
    $port = null,
    $charset = null,
    $dialect = null;
  private static ?string
    $user = null,
    $pass = null;

  public static function config(array $config, string $user, string $pass)
  {
    self::$config = $config;
    self::$user = $user;
    self::$pass = $pass;
  }

  private static function getDriver()
  {
    if (is_null(self::$driver)) {
      self::$driver = self::$config['driver']
        ?? Env::getEnvOrValue("DB_DRIVER", "mysql");
    }
    return self::$driver;
  }

  public static function getAttribute(string $attribute)
  {
    if (is_null(self::${$attribute})) {
      self::${$attribute} = self::$config[$attribute]
        ?? Env::getEnvOrValue("DB_" . strtoupper($attribute), null);
    }
    return self::${$attribute};
  }

  public static function getUser()
  {
    if (is_null(self::$user)) {
      self::$user = Env::getEnvOrValue("DB_USER", "root");
    }
    return self::$user;
  }

  public static function getPass()
  {
    if (is_null(self::$pass)) {
      self::$pass = Env::getEnvOrValue("DB_PASS", "");
    }
    return self::$pass;
  }

  private static function getConfig()
  {
    $driver = self::getDriver();
    $config = $driver . ":";
    $keys = ["dbname", "host", "port", "charset", "dialect"];
    foreach ($keys as $key) {
      $value = self::getAttribute($key);
      if ($value) {
        $config .= "$key=$value;";
      }
    }
    return $config;
  }

  private function connect()
  {
    if (is_null($this->pdo)) {
      $this->setConnection();
    }
  }

  private function setConnection()
  {
    $config = self::getConfig();
    $this->pdo = new PDO($config, self::getUser(), self::getPass());
    // throw exceptions, when SQL error is caused
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // prevent emulation of prepared statements
    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  }

  public function execute(string $query, $params = [])
  {
    $this->connect();
    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    return $stmt;
  }
}
