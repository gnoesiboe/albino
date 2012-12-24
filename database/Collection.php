<?php

namespace Albino\Database;

/**
 * Collection class.
 *
 * @package    Albino
 * @subpackage Collection
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
class Collection implements \IteratorAggregate, \Countable
{

  /**
   * @var array
   */
  protected $data;

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    $this->data = $this->prepareData($data);
  }

  /**
   * @param $data
   * @return array
   */
  protected function prepareData($data)
  {
    return $data;
  }

  /**
   * Retrieve an external iterator
   *
   * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
   *
   * @return \Traversable    An instance of an object implementing <b>Iterator</b> or <b>Traversable</b>
   */
  public function getIterator()
  {
    return new \ArrayIterator($this->data);
  }

  /**
   * Count elements of an object
   *
   * @link http://php.net/manual/en/countable.count.php
   *
   * @return int      The custom count as an integer. The return value is cast to an integer.
   */
  public function count()
  {
    return count($this->data);
  }

  /**
   * @param mixed $item       Item to be saved in the collection
   * @param string $key       Optional key that this item is saved on for easy access via get
   *
   * @return Collection
   */
  public function add($item, $key = null)
  {
    if (is_null($key) === true)
    {
      $this->data[] = $item;
    }
    else
    {
      $this->data[$key] = $item;
    }

    return $this;
  }

  /**
   * @param string $key
   * @return bool
   */
  public function has($key)
  {
    return array_key_exists($key, $this->data);
  }

  /**
   * @param string $key
   * @return mixed
   */
  public function get($key)
  {
    $this->validateHas($key);
    return $this->data[$key];
  }

  /**
   * @param string $key
   * @throws \Exception
   *
   * @todo extend exception for easy exception filtering
   */
  public function validateHas($key)
  {
    if ($this->has($key) === false)
    {
      throw new \Exception(sprintf('No item in the collection with key: '), $key);
    }
  }

  /**
   * @return bool
   */
  public function hasData()
  {
    return $this->count() > 0;
  }

  /**
   * @throws \Exception
   */
  protected function validateHasData()
  {
    if ($this->hasData() === false)
    {
      throw new \Exception('Collection doesn\'t have data');
    }
  }

  /**
   * @return mixed
   */
  public function getFirst()
  {
    $this->validateHasData();

    list($firstKey) = array_keys($this->data);

    return $this->data[$firstKey];
  }
}
