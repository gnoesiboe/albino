<?php

namespace Albino;

use Albino\Configurable;

/**
 * Route class.
 *
 * @package    Albino
 * @subpackage Route
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
abstract class Route extends Configurable
{

  /**
   * @var array
   */
  protected $requiredOptions = array('action', 'pattern');

  /**
   * @var array
   */
  protected $options = array();

  /**
   * @var array
   */
  protected $params = array();

  /**
   * Constructor
   *
   * @param array $options
   */
  public function __construct(array $options = array())
  {
    parent::__construct($options);

    $this->validateOptions();
  }

  /**
   * @return string
   */
  public function getAction()
  {
    return $this->getOption('action');
  }

  /**
   * @return string
   */
  protected function getPattern()
  {
    return $this->options['pattern'];
  }

  /**
   * @param string $key
   * @param mixed $default
   *
   * @return mixed
   */
  public function getParam($key, $default = null)
  {
    return $this->hasParam($key) === true ? $this->params[$key] : $default;
  }

  /**
   * @param string $key
   * @return bool
   */
  public function hasParam($key)
  {
    return array_key_exists($key, $this->params);
  }

  /**
   * @param $key
   * @param $value
   */
  public function setParam($key, $value)
  {
    $this->params[$key] = $value;
  }

  /**
   * @param array $params
   */
  public function setParams(array $params)
  {
    foreach ($params as $key => $value)
    {
      $this->setParam($key, $value);
    }
  }

  /**
   * @param Request $request
   * @return bool
   */
  abstract public function match(Request $request);
}
