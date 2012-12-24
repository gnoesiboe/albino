<?php

namespace Albino\Database;

/**
 * DatabaseManager class.
 *
 * @package    Albino
 * @subpackage <subpackage
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
class DatabaseManager
{

  /**
   * @var array
   */
  protected $connections = array();

  /**
   * @var array
   */
  protected $tables = array();

  /**
   * @static
   * @var DatabaseManager
   */
  protected static $instance;

  /**
   * @return DatabaseManager
   */
  public static function getInstance()
  {
    if (self::$instance instanceof DatabaseManager)
    {
      return self::$instance;
    }

    self::$instance = new DatabaseManager();

    return self::$instance;
  }

  /**
   * Constructor
   */
  protected function __construct()
  {
    $this->validateRequirements();
  }

  /**
   * Validates wether or not this library has the
   * required modules installed
   *
   * @throws Exception
   */
  protected function validateRequirements()
  {
    //@todo implement
  }

  /**
   * @param string $identifier
   * @param Connection $connection
   */
  public function addConnection($identifier, Connection $connection)
  {
    $this->connections[$identifier] = $connection;
  }

  /**
   * @param string $identifier
   * @return Connection
   */
  public function getConnection($identifier)
  {
    $this->validateHasConnection($identifier);

    return $this->connections[$identifier];
  }

  /**
   * @param string $identifier
   */
  public function closeConnection($identifier)
  {
    $this->getConnection($identifier)->close();
  }

  /**
   * @param string $identifier
   * @throws Exception
   */
  public function validateHasConnection($identifier)
  {
    if ($this->hasConnection($identifier) === false)
    {
      throw new Exception(sprintf('No connection added with the identifier: %s', $identifier));
    }
  }

  /**
   * @param string $identifier
   * @return bool
   */
  public function hasConnection($identifier)
  {
    return array_key_exists($identifier, $this->connections);
  }

  /**
   * @param $identifier
   * @param Connection $connection      If omitted the first added connection is used
   *
   * @return Table
   */
  public function getTable($identifier, Connection $connection = null)
  {
    if ($this->hasTable($identifier) === false)
    {
      $className = $this->prepareTableIdentifier($identifier);

      $this->tables[$identifier] = new $className();
    }

    $this->tables[$identifier]->setConnection($connection instanceof Connection ? $connection : $this->getDefaultConnection());

    return $this->tables[$identifier];
  }

  /**
   * @return \Albino\Database\Connection
   * @throws \Exception
   */
  protected function getDefaultConnection()
  {
    if (count($this->connections) === 0)
    {
      throw new \Exception('No connections defined');
    }

    $keys = array_keys($this->connections);
    $firstKey = $keys[0];

    return $this->connections[$firstKey];
  }

  /**
   * @param string $identifier
   * @return string
   */
  public function prepareTableIdentifier($identifier)
  {
    $prefix = 'Application\Table\\';

    if (strpos($identifier, 'Table') === false)
    {
      $identifier = $identifier . 'Table';
    }

    return $prefix . $identifier;
  }

  /**
   * @param string $identifier
   * @return bool
   */
  protected function hasTable($identifier)
  {
    return array_key_exists($identifier, $this->tables);
  }
}