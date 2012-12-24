<?php

namespace Albino;

/**
 * @author     Wesley van Opdorp <wesley@freshheads.com>
 */
class Autoloader
{

  /**
   * Static constainer for the autoloader instance.
   *
   * @var Autoloader
   */
  protected static $instance;

  /**
   * Boolean wether the autoloader is registered.
   *
   * @var bool
   */
  protected static $registered = false;

  /**
   * Attempts to register the Albino autoload method.
   *
   * @author Wesley van Opdorp <thorgzor@gmail.com>
   * @static
   * @return bool
   */
  static public function register()
  {
    if (self::$registered === false)
    {
      $result = spl_autoload_register(array(self::getInstance(), 'load'));
      self::$registered = $result;
    }

    return self::$registered;
  }

  /**
   * Attempts to unregister the Albino autoload method.
   *
   * @author Wesley van Opdorp <thorgzor@gmail.com>
   * @static
   * @return bool
   */
  static public function unregister()
  {
    $result = spl_autoload_unregister(array(self::getInstance(), 'load'));
    self::$registered = $result;
    return $result;
  }

  /**
   * Method responsible for loading actual classes.
   *
   * @author Wesley van Opdorp <thorgzor@gmail.com>
   * @param string $className
   * @return bool
   */
  protected function load($className)
  {
    // Attempt to autoload only Albino classes.
    if (substr($className, 0, strlen(__NAMESPACE__)) !== __NAMESPACE__) {
      return false;
    }

    // Parse the class filename.
    $classLocation = sprintf(
      '%s%s%s.php',
      dirname(dirname(__FILE__)), // Albino root directory
      DIRECTORY_SEPARATOR,
      str_replace("\\", "/", $className)
    );

    require_once($classLocation);
    return true;
  }

  /**
   * Fetches a autoloader instance, is a singleton.
   * Exists so we can unregister the exact same instance.
   *
   * @author Wesley van Opdorp <thorgzor@gmail.com>
   * @return Autoloader
   */
  protected function getInstance()
  {
    if (is_null(self::$instance) === true) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Basic method to check whether the autoloader
   * has been registered.
   *
   * @author Wesley van Opdorp <thorgzor@gmail.com>
   * @return bool
   */
  public function isRegistered() {
    return (bool) self::$registered;
  }
}