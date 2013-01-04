<?php

namespace Albino;

use Albino\Route;
use Albino\Request;

/**
 * Router class.
 *
 * @package    <package>
 * @subpackage <subpackage
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
abstract class Router
{

  /**
   * @var array
   */
  protected $routes = array();

  /**
   * @var Route
   */
  protected $currentRoute;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->setup();
    $this->configure();
  }

  /**
   * Setup router
   */
  protected function setup()
  {

  }

  /**
   * Set a route on this router
   *
   * @param $key
   * @param Route $route
   */
  protected function setRoute($key, Route $route)
  {
    $this->routes[$key] = $route;
  }

  /**
   * @param array $routes
   */
  protected function setRoutes(array $routes)
  {
    foreach ($routes as $key => $route)
    {
      $this->setRoute($key, $route);
    }
  }

  /**
   * @param Request $request
   * @return bool
   */
  public function match(Request $request)
  {
    foreach ($this->routes as $route)
    {
      /* @var Route $route */

      if ($route->match($request) === true)
      {
        $this->currentRoute = $route;
        return true;
      }
    }

    return false;
  }

  /**
   * @return Route
   */
  public function getCurrentRoute()
  {
    return $this->currentRoute;
  }

  /**
   * Implement this function to set the application's routes
   */
  abstract protected function configure();
}
