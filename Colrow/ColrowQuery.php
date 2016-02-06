<?php

namespace Colrow;

/**
 * ColrowQuery - Handles querying data from Colrow.
 *
 * @author Somin Kobayashi <somin@rashikucorp.com>
 */
class ColrowQuery
{
  /**
   * Where constraints.
   *
   * @var array
   */
  private $where = [];

  /**
   * Order By keys.
   *
   * @var string
   */
  private $orderby;

  /**
   * Set the query orderBy to descending if this value is true.
   *
   * @var bool
   */
  private $reverse;

  /**
   * Offset from the beginning of the search results.
   *
   * @var int
   */
  private $offset;

  /**
   * Limit of results.
   *
   * @var int
   */
  private $limit;

  public function equalTo($key, $value)
  {
    $this->where[$key] = $value;
    return $this;
  }

  private function addCondition($key, $condition, $value)
  {
    if (!isset($this->where[$key])) {
      $this->where[$key] = [];
    } else if (!is_array($this->where[$key])) {
      $temp = $this->where[$key];
      $this->where[$key] = ['=' => $temp];
    }
    $this->where[$key][$condition] = $value;
  }

  public function notEqualTo($key, $value)
  {
    $this->addCondition($key, '<>', $value);
    return $this;
  }

  public function lessThan($key, $value)
  {
    $this->addCondition($key, '<', $value);
    return $this;
  }

  public function greaterThan($key, $value)
  {
    $this->addCondition($key, '>', $value);
    return $this;
  }

  public function lessThanOrEqualTo($key, $value)
  {
    $this->addCondition($key, '<=', $value);
    return $this;
  }

  public function greaterThanOrEqualTo($key, $value)
  {
    $this->addCondition($key, '>=', $value);
    return $this;
  }

  public function exists($key)
  {
    $this->addCondition($key, '<>', '');
    return $this;
  }

  public function doesNotExist($key)
  {
    $this->addCondition($key, '=', '');
    return $this;
  }

  public function orQuery()
  {
    $array = [];
    foreach (func_get_args() as $arg) {
      $array[] = $arg->where;
    }
    $this->where['_or'] = $array;
    return $this;
  }

  public function orderBy($value)
  {
    $this->orderby = $value;
    return $this;
  }

  public function reverse($flag)
  {
    $this->reverse = $flag ? 'true' : 'false';
    return $this;
  }

  public function offset($number)
  {
    $this->offset = $number;
    return $this;
  }

  public function limit($number)
  {
    $this->limit = $number;
    return $this;
  }

  public function find()
  {
    $options = $this->_getOptions();
    if (isset($options['where'])) {
      $options['where'] = json_encode($options['where']);
    }
    $response = ColrowClient::_request('GET', $options);
    return ColrowObject::_createObjectsFromFeeds($response['result']['feeds']);
  }

  public function first()
  {
    $objects = $this->find();
    if (count($objects)) {
      return $objects[0];
    }
    return null;
  }

  public function count()
  {
    return count($this->find());
  }

  public function _getOptions()
  {
    $options = [];
    if (!empty($this->where)) {
      $options['where'] = $this->where;
    }
    if ($this->orderby) {
      $options['orderby'] = $this->orderby;
    }
    if ($this->reverse) {
      $options['reverse'] = $this->reverse;
    }
    if ($this->offset) {
      $options['offset'] = $this->offset;
    }
    if ($this->limit) {
      $options['limit'] = $this->limit;
    }
    return $options;
  }
}