<?php

namespace Albino;

/**
 * Registry class.
 *
 * @package    Albino
 * @subpackage Registry
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
class Registry
{

  /**
   * @var array
   */
  protected static $data = array();

  /**
   * @param string $key
   * @param mixed $default
   *
   * @return mixed
   */
  public static function get($key, $default = null)
  {
    if (self::has($key) === false)
    {
      return $default;
    }

    return self::$data[$key];
  }

  /**
   * @param string $key
   * @return bool
   */
  public static function has($key)
  {
    return array_key_exists($key, self::$data);
  }

  /**
   * @param string $key
   * @throws \Exception
   */
  public static function clear($key)
  {
    if (self::has($key) === false)
    {
      throw new \Exception(sprintf('Key: %s doesn\'t exist', $key));
    }

    self::$data[$key] = null;
  }

  /**
   * @param string $key
   * @param mixed $value
   */
  public static function set($key, $value)
  {
    self::$data[$key] = $value;
  }

  /**
   * @return array
   */
  public static function getData()
  {
    return self::$data;
  }
}
