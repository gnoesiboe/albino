<?php

namespace Albino;

/**
 * Autoloader class.
 *
 * @package    Albino
 * @subpackage Autoloader
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
class Autoloader
{

  /**
   * @var array
   */
  protected $namespaces = array();

  /**
   * @return array
   */
  public function getNamespaces()
  {
    return $this->namespaces;
  }

  /**
   * @param string $namespace
   * @return bool
   */
  public function hasNamespace($namespace)
  {
    return array_key_exists($namespace, $this->namespaces);
  }

  /**
   * @param string $namespace
   * @param array|string $paths
   */
  public function addNamespace($namespace, $paths)
  {
    $this->namespaces[$namespace] = (array) $paths;
  }

  /**
   * @param array $namespaces
   * @return Autoloader
   */
  public function addNamespaces(array $namespaces)
  {
    foreach ($namespaces as $namespace => $paths)
    {
      $this->addNamespace($namespace, $paths);
    }

    return $this;
  }

  /**
   * @param string $class
   */
  public function requireClass($class)
  {
    if ($file = $this->getFileForClass($class))
    {
      require_once $file;
    }
  }

  /**
   * @param string $class
   * @return string
   */
  public function getFileForClass($class)
  {
    if ($class[0] === '\\')
    {
      $class = substr($class, 1);
    }

    $posDevider = strpos($class, '\\');
    if ($posDevider === false)
    {
      return null;
    }

    $namespace = substr($class, 0, $posDevider);
    if ($this->hasNamespace($namespace) === false)
    {
      return null;
    }

    $namespacePath = substr($class, $posDevider + 1);

    foreach ($this->namespaces[$namespace] as $path)
    {
      $filePath = $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespacePath) . '.php';

      if (file_exists($filePath) === true)
      {
        return $filePath;
      }
    }

    return null;
  }

  /**
   * @param boolean $prepend
   * @return Autoloader
   */
  public function register($prepend = false)
  {
    spl_autoload_register(array($this, 'requireClass'), true, $prepend);
    return $this;
  }
}