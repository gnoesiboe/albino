<?php

namespace Albino;

use Albino\Exception;

/**
 * Configurable class.
 *
 * @package    Albino
 * @subpackage Configurable
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
abstract class Configurable
{

  /**
   * @var array
   */
  protected $requiredOptions = array();

  /**
   * @var array
   */
  protected $options = array();

  /**
   * @param array $options
   */
  public function __construct(array $options = array())
  {
    $this->options = $options;
  }

  /**
   * @param string $key
   */
  public function getOption($key)
  {
    $this->validateHasOption($key);
    return $this->options[$key];
  }

  /**
   * @param string $key
   * @throws Exception\Validation
   */
  public function validateHasOption($key)
  {
    if ($this->hasOption($key) === false)
    {
      throw new Exception\Validation(sprintf('No option with key: %s set', $key));
    }
  }

  /**
   * @param string $key
   * @param mixed $value
   */
  public function setOption($key, $value)
  {
    $this->options[$key] = $value;
  }

  /**
   * @param string $key
   * @return bool
   */
  public function hasOption($key)
  {
    return array_key_exists($key, $this->options);
  }

  /**
   * Validates the supplied options
   */
  public function validateOptions()
  {
    $this->validateRequiredOptionsSet();
    $this->validateOptionKeyValues();
  }

  /**
   * @param array $keys         Optional array containing the option keys to validate. If omitted, all keys will be validated
   * @throws Exception\Schema
   */
  protected function validateOptionKeyValues($keys = array())
  {
    $allKeys = true;
    if (count($keys) > 0)
    {
      $allKeys = false;
    }

    $exceptionSchema = new Exception\Schema;

    foreach ($this->options as $key => $value)
    {
      if ($allKeys === false && in_array($key, $keys) === false)
      {
        continue;
      }

      $validationMethodName = 'validateOption' . ucfirst($key);

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
   * @return bool
   */
  protected function validateRequiredOptionsSet()
  {
    if (count($this->requiredOptions) === 0)
    {
      return true;
    }

    foreach ($this->requiredOptions as $key)
    {
      $this->validateHasOption($key);
    }

    return true;
  }
}
