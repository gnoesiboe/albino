<?php

namespace Albino;

use Albino\Collection\ModelCollection;

/**
 * Table class.
 *
 * @package    Albino
 * @subpackage Table
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
abstract class Table
{

  /**
   * @var Connection
   */
  protected $connection = null;

  /**
   * @param Connection $connection
   */
  public function setConnection(Connection $connection)
  {
    $this->connection = $connection;
  }

  /**
   * @return Connection
   */
  protected function getConnection()
  {
    $this->validateHasConnection();

    return $this->connection;
  }

  /**
   * @return bool
   */
  public function hasConnection()
  {
    return $this->connection instanceof Connection;
  }

  /**
   * @throws \Exception
   */
  protected function validateHasConnection()
  {
    if ($this->hasConnection() === false)
    {
      throw new \Exception('No connection set');
    }
  }

  /**
   * @param \PDOStatement $stmt
   * @param \Closure $modelGenerator
   *
   * @return ModelCollection
   */

  /**
   * @param \PDOStatement $stmt
   * @param \Closure $modelGenerator
   *
   * @throws \Exception
   *
   * @return ModelCollection
   */
  protected function generateCollection(\PDOStatement $stmt = null, \Closure $modelGenerator = null)
  {
    if (($modelGenerator instanceof \Closure) === false)
    {
      $modelGenerator = $this->getModelGenerator();
    }

    $return = new ModelCollection();

    if (is_null($stmt) === true)
    {
      return $return;
    }

    $modelClassName = $this->getModelClassName();

    while ($data = $stmt->fetch(\PDO::FETCH_ASSOC))
    {
      /* @var array $row */

      $return->add($modelGenerator($modelClassName, $data));
    }

    return $return;
  }

  /**
   * @param $modelClassName
   * @param array $data
   *
   * @return \Albino\Model;
   */
  protected function generateModel($modelClassName, array $data = array())
  {
    $generator = $this->getModelGenerator();
    return $generator($modelClassName, $data);
  }

  /**
   * @return \Closure
   * @throws \Exception
   */
  protected function getModelGenerator()
  {
    return function($modelClassName, array $data) {
      if (class_exists($modelClassName) === false)
      {
        throw new \Exception(sprintf('Model class: %s doesn\'t exist', $modelClassName));
      }

      return new $modelClassName($data);
    };
  }

  /**
   * @return string
   */
  protected function getModelClassName()
  {
    $tableClassName = end(explode('\\', get_class($this)));

    $modelName = preg_replace('/Table$/i', '', $tableClassName);

    return 'Application\Model\\' . $modelName;
  }
}