<?php

namespace Pkit\Database;

use Pkit\Utils\Sanitize;
use PDO;

class Table
{
  private string $_table;
  private Database $_database;

  public function __construct(array $properties=[])
  {
    foreach($properties as $key => $value){
      $this->{$key} = $value;
    }
    $this->_table = Sanitize::sanitizeClass(get_class($this));
    $this->_database = new Database;
  }

  public function __set($prop, $value)
  {
    return $this->$prop = $value;
  }

  public function __get($prop)
  {
    return $this->$prop;
  }

  static private function binds($keys)
  {
    return implode(', ', array_pad([], count($keys), '?'));
  }

  static private function fields($keys)
  {
    return implode(', ', array_map(function ($value) {
      return "`$value`";
    }, $keys));
  }

  public function insert(?string $return = null)
  {
    $array = Sanitize::sanitizeProperties((array)$this);
    $array = array_filter($array);

    $keys = array_keys($array);
    $fields = self::fields($keys);
    $binds  = self::binds($keys);
    $return  = " RETURNING $return" ?? "";

    $query = "INSERT INTO $this->_table ( $fields ) VALUES ( $binds ) $return";

    $stmt = $this->_database->execute($query, array_values($array));

    if ($return) {
      return $stmt->fetch();
    }
  }

  public function select(array $where = null, string $orderBy = null, array $limit = null)
  {
    $array = Sanitize::sanitizeProperties((array)$this);

    $keys = array_keys($array);
    $fields = self::fields($keys);
    $params = [];

    if ($where) {
      [$where, $wheres] = self::where($where);
      $params = array_merge($params, $wheres);
    }

    $where = $where ?? "";
    $order = strlen($orderBy) ? 'ORDER BY ' . $orderBy : '';
    $order = !empty($limit) ? self::limit($limit) : '';

    $query = "SELECT $fields FROM $this->_table $where $order $limit";

    $stmt = $this->_database->execute($query, array_values($params));

    return $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this));
  }

  public function update(array $where = null)
  {
    $array = Sanitize::sanitizeProperties((array)$this);
    $array = array_filter($array);

    $fields = '';
    foreach ($array as $field => $_) {
      $fields .= "$field=?, ";
    }
    $fields = rtrim($fields, ", ");

    $params = $array;

    if ($where) {
      [$where, $wheres] = self::where($where);
      $params = array_merge($params, $wheres);
    }

    $where = $where ?? "";

    $query = "UPDATE $this->_table SET $fields $where";

    $this->_database->execute($query, array_values($params));
  }

  public function delete(array $where = null)
  {
    $params = [];
    if ($where) {
      [$where, $wheres] = self::where($where);
      $params = array_merge($params, $wheres);
    }
    $where = $where ?? "";

    $query = "DELETE FROM $this->_table $where";

    $this->_database->execute($query, array_values($params));
  }

  private static function where(array $where)
  {
    $binds = [];
    if (!empty($where)) {
      $query = 'WHERE';
      foreach ($where as $field => $bind) {
        if (!is_int($field)) {
          @[$field, $comp] = explode(":", $field);
          $query .= (" `$field`" . ($comp ?? '=') . '?,');
          $binds[] = $bind;
        } else {
          $query .= " $bind,";
        }
      }
      $query = rtrim($query, ",");
    }
    return [$query ?? "", $binds];
  }

  private static function limit(array $limit)
  {
    $init = $limit[0];
    $final = $limit[1];
    return " LIMIT $init, $final";
  }
}
