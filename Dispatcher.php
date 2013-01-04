<?php

namespace Albino;

use Albino\Request;

/**
 * Dispatcher class.
 *
 * @package    Albino
 * @subpackage Dispatcher
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class Dispatcher
{

  /**
   * @var Request
   */
  protected $request;

  /**
   * @param Request $request
   * @return Dispatcher
   */
  public function setRequest(Request $request)
  {
    $this->request = $request;

    return $this;
  }

  /**
   * Forward the client to the supplied route
   *
   * @param Route $route
   */
  public function forwardTo(Route $route)
  {
    die('@todo: ' . __METHOD__ . ' - (line: ' . __LINE__ . ', file: ' . __FILE__ . ')');
  }
}