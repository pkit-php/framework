<?php

namespace Pkit\Database;

use Pkit\Utils\Text;

class QueryBuilder
{
  private array
    $commands = [],
    $params = [];
  private ?string
    $table,
    $as,
    $query = '';

  public function __construct(string $table, ?string $as = null)
  {
    $this->table = $table;
    $this->as = $as;
  }

  public function __toString()
  {
    $this->query = "";
    $this->params = [];
    foreach ($this->commands as $key => $command) {
      if ($command["type"] == "insert" && $this->commands[$key + 1]["type"] == "select") {
        $this->{"setInsertSelect"}($command);
      } else {
        $this->{"set" . ucwords($command["type"])}($command);
      }
    }
    return $this->query;
  }

  public function getParams()
  {
    return $this->params;
  }

  static private function binds(array $keys)
  {
    return implode(', ', array_pad([], count($keys), '?'));
  }

  static private function fields(array $keys)
  {
    return implode(', ', array_map(function ($value) {
      return "$value";
    }, $keys));
  }

  static private function relations($relations, $bind)
  {
    $binds = [];
    $query = '';
    foreach ($relations as $field => $bindValue) {
      if (!is_int($field)) {
        @[$field, $comp] = explode(":", $field);
        $query .= ("$field" . ($comp ?? '=') . ($bind ? "$bindValue, " : '?, '));
        if (!$bind) {
          $binds[] = $bindValue;
        }
      } else {
        $query .= "$bindValue, ";
      }
    }
    $query = rtrim($query, ", ");
    return [$query, $binds];
  }

  static private function mapTableAlias(array $array, ?string $keyAlias = null, ?string $valueAlias = null)
  {
    $arrayValues = array_values($array);
    $arrayKeys = array_keys($array);
    if ($keyAlias) {
      $arrayValues = array_map(function ($fieldName) use ($keyAlias) {
        if (strpos($fieldName, ".")) {
          return $fieldName;
        }
        return $keyAlias . $fieldName;
      }, $arrayValues);
    }
    if ($valueAlias) {
      $arrayKeys = array_map(function ($fieldName) use ($valueAlias) {
        if (strpos($fieldName, ".")) {
          return $fieldName;
        }
        return $valueAlias . $fieldName;
      }, $arrayKeys);
    }
    return array_combine($arrayKeys, $arrayValues);
  }

  private function getTableOrThisTable($table)
  {
    if ($table) {
      return $table;
    } else {
      return $this->table;
    }
  }

  private function getAsOrThisTable($as)
  {
    if ($as) {
      return "AS $as";
    } else if ($this->as) {
      return "AS $this->as";
    }
    return null;
  }

  private function getTableAlias($table, $as)
  {
    if ($as) {
      $as = Text::removeFromStart($as, "AS ");
      return "$as.";
    } else if ($table) {
      return "$table.";
    }
    return "";
  }

  public function select(array $select, ?string $table = null, ?string $as = null)
  {
    $this->commands[] = [
      "type" => "select",
      "fields" => array_values($select),
      "table" => $table,
      "as" => $as
    ];
    return $this;
  }

  private function setSelect(array $command)
  {
    $as = $this->getAsOrThisTable($command["as"]);
    $table = $this->getTableOrThisTable($command["table"]);
    $tableAlias = $this->getTableAlias($table, $as);
    $fieldsTable = $this->mapTableAlias($command["fields"], null, $tableAlias);

    $fields = self::fields($fieldsTable);

    $this->query .= "SELECT $fields FROM $table $as ";
  }

  private function setInsertSelect(array $command)
  {
    $fields = self::fields($command["params"]);

    $this->query .= "INSERT INTO $this->table ( $fields ) ";
  }

  public function insert(array $insert)
  {
    $this->commands[] = [
      "type" => "insert",
      "fields" => array_keys($insert),
      "params" => array_values($insert)
    ];
    return $this;
  }

  private function setInsert(array $command)
  {
    $binds = self::binds($command["params"]);
    $fields = self::fields($command["fields"]);

    $this->params = array_merge($this->params, $command["params"]);
    $this->query .= "INSERT INTO $this->table ( $fields ) VALUES ( $binds ) ";
  }

  public function update(array $update)
  {
    $this->commands[] = [
      "type" => "update",
      "update" => $update
    ];
    return $this;
  }

  private function setUpdate(array $command)
  {
    $table = $this->table;
    $as = $this->as ? "AS $this->as" : "";

    $tableAlias = $this->getTableAlias($table, $as);
    $update = $this->mapTableAlias($command["update"], null, $tableAlias);

    [$query, $binds] = self::relations($update, false);

    $this->params = array_merge($this->params, $binds);
    $this->query .= "UPDATE $this->table $as SET $query ";
  }

  public function innerJoin(array $on, string $table, string $as = null)
  {
    $this->commands[] = [
      "type" => "innerJoin",
      "on" => $on,
      "table" => $table,
      "as" => $as
    ];
    return $this;
  }

  private function setInnerJoin(array $command)
  {
    $table = $command["table"];
    $as = $command["as"] ? ("AS " . $command["as"]) : "";
    $thisTableAlias = $this->as . ".";

    $tableAlias = $this->getTableAlias($table, $as);
    $on = $this->mapTableAlias($command['on'], $thisTableAlias, $tableAlias);

    [$query, $binds] = self::relations($on, true);

    $this->params = array_merge($this->params, $binds);
    $this->query .= "INNER JOIN $table $as ON $query ";
  }

  public function and(array $and = null)
  {
    $this->commands[] = [
      "type" => "and",
      "and" => $and
    ];
    return $this;
  }

  private function setAnd(array $command)
  {
    $as = $this->getAsOrThisTable($command["as"]);
    $table = $this->getTableOrThisTable($command["table"]);
    $tableAlias = $this->getTableAlias($table, $as);
    $and = $this->mapTableAlias($command['and'], $tableAlias, $tableAlias);

    [$query, $binds] = self::relations($and, false);

    $this->params = array_merge($this->params, $binds);
    $this->query .= "AND " . $query;
  }

  public function or(array $or)
  {
    $this->commands[] = [
      "type" => "or",
      "or" => $or
    ];
    return $this;
  }

  private function setOr(array $command)
  {
    $as = $this->getAsOrThisTable($command["as"]);
    $table = $this->getTableOrThisTable($command["table"]);
    $tableAlias = $this->getTableAlias($table, $as);
    $or = $this->mapTableAlias($command['or'], $tableAlias, $tableAlias);

    [$query, $binds] = self::relations($or, false);
    $this->params = array_merge($this->params, $binds);
    $this->query .= "OR " . $query;
  }

  public function not(array $not)
  {
    $this->commands[] = [
      "type" => "not",
      "not" => $not
    ];
    $this->query .= $this;
  }

  private function setNot(array $command)
  {
    $as = $this->getAsOrThisTable($command["as"]);
    $table = $this->getTableOrThisTable($command["table"]);
    $tableAlias = $this->getTableAlias($table, $as);
    $not = $this->mapTableAlias($command['not'], $tableAlias, $tableAlias);

    [$query, $binds] = self::relations($not, false);
    $this->params = array_merge($this->params, $binds);

    $this->query .= "NOT " . $query;
  }

  public function where(?array $where = null)
  {
    $this->commands[] = [
      "type" => "where",
      "where" => $where
    ];
    return $this;
  }

  private function setWhere(array $command)
  {
    if ($command["where"]) {
      $as = $this->getAsOrThisTable($command["as"]);
      $table = $this->getTableOrThisTable($command["table"]);
      $tableAlias = $this->getTableAlias($table, $as);
      $where = $this->mapTableAlias($command['where'], $tableAlias, $tableAlias);

      [$query, $binds] = self::relations($where, false);
      $this->params = array_merge($this->params, $binds);
    }
    $this->query .= "WHERE " . $query . " ";
  }
}
