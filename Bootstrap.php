<?php

namespace Albino;

use Albino\Registry;
use Autoloader;
use Application\Router;
use Albino\Parser;
use Albino\Request;
use Albino\Dispatcher;

require dirname(__FILE__) . '/Registry.php';
require dirname(__FILE__) . '/DataHolder.php';

/**
 * Bootstrap class.
 *
 * @package    Albino
 * @subpackage Boostrap
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
abstract class Bootstrap
{

  /**
   * @var string
   */
  protected $defaultEnvironment = 'prod';

  /**
   * @var Autoloader
   */
  protected $autoloader;

  /**
   * @var Router
   */
  protected $router;

  /**
   * @var Request
   */
  protected $request;

  /**
   * @var Dispatcher
   */
  protected $dispatcher;

  /**
   * @param string $env
   */
  public function __construct($env = null)
  {
    $this->setEnvironment($env);
    $this->definePaths();
    $this->initAutoLoader();
    $this->initConfiguration();
  }

  /**
   * Dispatches the application
   */
  public function dispatch()
  {
    if ($this->isCalledCommandLine() === true)
    {
      //@todo
      die('@todo: ' . __METHOD__ . ' - (line: ' . __LINE__ . ', file: ' . __FILE__ . ')');
    }
    else
    {
      $router = $this->getRouter();
      $request = $this->getRequest();

      $success = $router->match($request);
      if ($success === false)
      {
        throw new Exception('No route matches. Insert a fallback route.');
      }

      $this->getDispatcher()
        ->setRequest($request)
        ->forwardTo($router->getCurrentRoute())
      ;
    }
  }

  /**
   * @return Dispatcher
   */
  protected function getDispatcher()
  {
    if ($this->dispatcher instanceof Dispatcher)
    {
      return $this->dispatcher;
    }

    $this->dispatcher = new Dispatcher();
    return $this->dispatcher;
  }

  /**
   * @return Request
   */
  protected function getRequest()
  {
    if ($this->request instanceof Request)
    {
      return $this->request;
    }

    $this->request = new Request();
    return $this->request;
  }

  /**
   * @return Router
   */
  protected function getRouter()
  {
    if ($this->router instanceof Router)
    {
      return $this->router;
    }

    $this->router = new Router();
    return $this->router;
  }

  /**
   * @return bool
   */
  protected function isCalledCommandLine()
  {
    return php_sapi_name() === 'cli';
  }

  /**
   * Initiates the application
   */
  protected function initConfiguration()
  {
    $file = $this->getConfigFilePath();

    if (file_exists($file) === false)
    {
      throw new \Exception(sprintf('Config file: \'%s\' doesn\'t exist', $file));
    }

    Registry::set('config', $this->getConfigParser()->parse($file, Registry::get('env')));
  }

  /**
   * @return Parser\Ini
   */
  protected function getConfigParser()
  {
    return new Parser\Ini();
  }

  /**
   * @return string
   */
  protected function getConfigFilePath()
  {
    return Registry::get('path')->get('application') . '/config/config.ini';
  }

  /**
   * Defines this application server paths
   */
  protected function definePaths()
  {
    Registry::set('path', new DataHolder(array(
      'project' => $this->getProjectPath(),
      'application' => $this->getApplicationPath(),
      'library' => $this->getLibraryPath(),
      'public' => $this->getPublicPath()
    )));
  }

  /**
   * @return string
   */
  protected function getPublicPath()
  {
    return realpath(dirname(__FILE__) . '/../../public');
  }

  /**
   * @return string
   */
  protected function getLibraryPath()
  {
    return realpath(dirname(__FILE__) . '/../../lib');
  }

  /**
   * @return string
   */
  protected function getApplicationPath()
  {
    return realpath(dirname(__FILE__) . '/../../application');
  }

  /**
   * @return string
   */
  protected function getProjectPath()
  {
    return realpath(dirname(__FILE__) . '/../../');
  }

  /**
   * Initiates the application's autoloader
   */
  protected function initAutoLoader()
  {
    require dirname(__FILE__) . '/../../application/Autoloader.php';
    $this->autoloader = new Autoloader();
    $this->autoloader
      ->addNamespaces($this->getAutoloaderNamespaces())
      ->register(true)
    ;
  }

  /**
   * @return array
   */
  protected function getAutoloaderNamespaces()
  {
    return array(
      'Application'     => Registry::get('path')->get('application'),
      'Albino'  => Registry::get('path')->get('library') . '/Albino'
    );
  }

  /**
   * @param string $env
   * @return Bootstrap
   */
  protected function setEnvironment($env = null)
  {
    Registry::set('env', $env);
  }
}
