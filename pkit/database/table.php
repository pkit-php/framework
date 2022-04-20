<?php namespace Pkit\Database;

use Pkit\Utils\Sanitize;

class Table
{
  private string $_table, $_idField;
  private Database $_database;

  public function __construct($table = null, $idField = 'Id')
  {
    $this->_table = $table ?? Sanitize::sanitizeClass(get_class($this));
    $this->_idField = $idField;
    $this->_database = new Database;
  }

  public function getProtectedValue($prop_name)
  {
    $array = (array) $this;
    $prefix = chr(0) . '*' . chr(0);
    return $array[$prefix . $prop_name];
  }

  static private function binds($keys)
  {
    return implode(', ', array_pad([], count($keys), '?'));
  }

  static private function fields($keys)
  {
    return implode(', ', $keys);
  }

  public function insert($returnId = false): mixed
  {
    // $array = Converter::objectToArray($this);
    // $array = array_filter($array);
    $array = (array)$this;
    var_dump($array);

    $keys = array_keys($array);
    $fields = self::fields($keys);
    $binds  = self::binds($keys);
    $return  = $returnId ? " RETURNING $this->_idField " : "";

    $query = "INSERT INTO $this->_table ( $fields ) VALUES ( $binds ) $return";

    $stmt = $this->_database->execute($query, array_values($array));

    if ($returnId) {
      $result = $stmt->fetch();
      $this[$this->_idField] = $result[0][$this->_idField];
    }
  }

  public function select(array $where = null, string $orderBy = null, string $limit = null)
  {
    // $array = Converter::objectToArray($this);
    $array = (array)$this;
    foreach ($array as $key => $value) {
      unset($array[$key]);
      if (!preg_match('/\\\/', $key)) {
        $protected = chr(0) . "*" . chr(0);
        $key = str_replace($protected, "", $key);
        $array[$key] = $value;
      }
    }
    $keys = array_keys($array);
    $fields = self::fields($keys);
    $params = [];

    if ($where) {
      [$where, $wheres] = self::where($where);
      $params = array_merge($params, $wheres);
    }

    $where = $where ?? "";
    $order = strlen($orderBy) ? 'ORDER BY ' . $orderBy : '';
    $order = strlen($limit) ? 'LIMIT ' . $limit : '';

    $query = "SELECT $fields FROM $this->_table $where $order $limit";

    $stmt = $this->_database->execute($query, $params);
    $result = [];
    while ($object = $stmt->fetchObject(get_class($this))) {
      $result[] = $object;
    }

    return $result;
  }

  private static function where(array $where)
  {
    $binds = [];
    if (!empty($where)) {
      $query = 'WHERE';
      foreach ($where as $field => $bind) {
        if (!is_int($field)) {
          [$field, $comp] = explode(":", $field);
          $query .= (" $field" . ($comp ?? '=') . '?,');
          $binds[] = $bind;
        } else {
          $query .= " $bind,";
        }
      }
      $query = rtrim($query, ",");
    }
    return [$query ?? "", $binds];
  }
}
