<?php

namespace Albino;

use Albino\Exception;

/**
 * DataHolder class.
 *
 * @package    <package>
 * @subpackage <subpackage
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class DataHolder
{

  /**
   * @var array
   */
  protected $data = array();

  /**
   * @var array
   */
  protected $requiredKeys = array();

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    $this->data = $data;
  }

  /**
   * @param string $key
   * @return mixed
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

  /**
   * @param string $key
   * @param mixed $value
   *
   * @return DataHolder
   */
  public function set($key, $value)
  {
    $this->data[$key] = $value;

    return $this;
  }

  /**
   * @param string $key
   * @return DataHolder
   */
  public function remove($key)
  {
    if ($this->has($key) === true)
    {
      unset($this->data[$key]);
    }

    return $this;
  }

  /**
   * @param array $data
   */
  public function setData(array $data)
  {
    $this->data = $data;
  }

  /**
   * Merges the supplied data with the data in this
   * data holder instance
   *
   * @param array $data
   */
  public function mergeData(array $data)
  {
    $this->data = array_merge($this->data, $data);
  }

  /**
   * @param array $data
   */
  public function fromArray(array $data)
  {
    foreach ($data as $key => $value)
    {
      if (is_array($value) === true)
      {
        $this->set($key, new DataHolder($data[$key]));
      }
      else
      {
        $this->set($key, $value);
      }
    }
  }

  /**
   * Validates the values for this data holder
   *
   * @return bool
   */
  public function validate()
  {
    $this->validateRequiredKeysSet();
    $this->validateKeyValues();
  }

  /**
   * @param array $keys         An array containing all keys to validate
   * @throws Exception\Schema
   */
  protected function validateKeyValues(array $keys = array())
  {
    $allKeys = true;
    if (count($keys) > 0)
    {
      $allKeys = false;
    }

    $exceptionSchema = new Exception\Schema;

    foreach ($this->data as $key => $value)
    {
      if ($allKeys === false && in_array($key, $keys) === false)
      {
        continue;
      }

      $validationMethodName = 'validate' . ucfirst($key);

      if (method_exists($this, $validationMethodName) === true)
      {
        try
        {
          $this->$validationMethodName($value);
        }
        catch (Exception $e)
        {
          $exceptionSchema->addException($e);
        }
      }
    }

    if ($exceptionSchema->hasExceptions() === true)
    {
      throw $exceptionSchema;
    }
  }

  /**
   * Validates wether or not all the required keys
   * are avialable for this data holder.
   *
   * @return bool
   * @throws Exception\Validation
   */
  protected function validateRequiredKeysSet()
  {
    if (count($this->requiredKeys) === 0)
    {
      return true;
    }

    $diff = array_diff($this->requiredKeys, array_keys($this->data));

    if (count($diff) > 0)
    {
      throw new Exception\Validation(sprintf('Missing required keys: %s', implode(', ', $diff)));
    }

    return true;
  }

  /**
   * @return bool
   */
  protected function checkRequiredKeysSet()
  {
    return count(array_diff($this->requiredKeys, array_keys($this->data))) === 0;
  }
}