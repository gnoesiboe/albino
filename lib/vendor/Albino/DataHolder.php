<?php

/**
 * DataHolder class.
 *
 * @package    <package>
 * @subpackage <subpackage
 * @author     <author>
 * @copyright  Freshheads BV
 */
class DataHolder
{

  /**
   * @var array
   */
  protected $data = array();

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    $this->data = $data;
  }

  /**
   * @param string $key
   * @return string
   */
  public function get($key)
  {
    return $this->data[$key];
  }

  /**
   * @param string $key
   * @return bool
   */
  public function has($key)
  {
    return array_key_exists($key, $this->data);
  }
}