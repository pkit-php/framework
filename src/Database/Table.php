<?php

namespace Pkit\Database;

use Pkit\Utils\Sanitize;
use PDO;

class Table
{
  private string $_table = "";
  private Connection $_connection;

  public function __construct(array $properties = [])
  {
    foreach ($properties as $key => $value) {
      $this->{$key} = $value;
    }
    $table = ((array)$this)["\0" . static::class . "\0table"];
    $this->_table = strlen($table) ?
      $table :
      Sanitize::sanitizeClass(get_class($this));
    $this->_connection = new Connection;
  }

  public function __set($prop, $value)
  {
    if (strpos($prop, 0, 1) == "_") {
      throw new \Exception("A propriedade $prop não pode ser definida, pois é privada", 500);
    }
    return $this->$prop = $value;
  }

  public function __get($prop)
  {
    if (strpos($prop, 0, 1) == "_") {
      throw new \Exception("A propriedade $prop não pode ser retornada, pois é privada", 500);
    }
    return $this->$prop;
  }

  public function insert(?array $returns = null)
  {
    $array = Sanitize::sanitizeProperties($this);
    $array = array_filter($array);

    $query = (new QueryBuilder($this->_table))->insert($array);

    if ($returns) {
      $query->return($returns);
    }

    $stmt = $this->_connection->execute($query, $query->getParams());

    return $stmt->fetch();
  }

  public function count(array $where = null)
  {
    $query = (new QueryBuilder($this->_table))->select(["COUNT(*)"]);

    if ($where) {
      $query->where($where);
    }

    $stmt = $this->_connection->execute($query, $query->getParams());

    return $stmt->fetch()[0];
  }

  public function select(array $where = null, array $orderBy = null, array $limit = null)
  {
    $array = Sanitize::sanitizeProperties($this);
    $keys = array_keys($array);

    $query = (new QueryBuilder($this->_table))->select($keys);

    if ($where) {
      $query->where($where);
    }
    if ($orderBy) {
      $query->order($orderBy);
    }
    if ($limit) {
      $query->limit($limit);
    }

    $stmt = $this->_connection->execute($query, $query->getParams());

    return $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this));
  }

  public function update(array $where = null)
  {
    $array = Sanitize::sanitizeProperties($this);
    $array = array_filter($array);

    $query = (new QueryBuilder($this->_table))->update($array);

    if ($where) {
      $query->where($where);
    }

    $this->_connection->execute($query, $query->getParams());
  }

  public function delete(array $where = null)
  {
    $query = (new QueryBuilder($this->_table))->delete();

    if ($where) {
      $query->where($where);
    }

    $this->_connection->execute($query, $query->getParams());
  }
}
