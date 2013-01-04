<?php

namespace Albino\Route;

use Albino\Route;
use Albino\Request;

/**
 * Literal class.
 *
 * @package    Albino
 * @subpackage Route
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class Literal extends Route
{

  /**
   * @param Request $request
   * @return bool
   */
  public function match(Request $request)
  {
    if ($request->getPath() === $this->getPattern())
    {
      return true;
    }

    return false;
  }
}