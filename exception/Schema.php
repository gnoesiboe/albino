<?php

namespace Albino\Exception;

use Albino\Exception;

/**
 * ExceptionSchema class.
 *
 * @package    Albino
 * @subpackage Exception
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class Schema extends Exception
{

  /**
   * @var array
   */
  protected $exceptions = array();

  /**
   * @param \Albino\Exception $e
   */
  public function addException(Exception $e)
  {
    $this->exceptions[] = $e;
  }

  /**
   * @return bool
   */
  public function hasExceptions()
  {
    return count($this->exceptions) > 0;
  }
}