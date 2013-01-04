<?php

namespace Albino\Route;

use Albino\Route;
use Albino\Request;

/**
 * Regex class.
 *
 * @package    Albino
 * @subpackage Route
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class Regex extends Route
{

  /**
   * @param Request $request
   * @return bool
   */
  public function match(Request $request)
  {
    $path = $request->getPath();

    if (preg_match($this->getPattern(), $path, $matches))
    {
      $this->toParams($matches);

      return true;
    }

    return false;
  }

  /**
   * Parses the parameters from the matches
   * and sets them for later retrieval in the
   * action.
   *
   * @param array $matches
   */
  protected function toParams($matches)
  {
    $params = array();

    foreach ($matches as $key => $value)
    {
      if (is_numeric($key) === false)
      {
        $this->setParam($key, $value);
      }
    }
  }
}
