<?php

require_once dirname(__FILE__) . '/collection/ModelCollection.php';

/**
 * Table class.
 *
 * @package    <package>
 * @subpackage <subpackage
 * @author     <author>
 * @copyright  Freshheads BV
 */
class Table
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
   * @throws Exception
   */
  protected function validateHasConnection()
  {
    if ($this->hasConnection() === false)
    {
      throw new Exception('No connection set');
    }
  }

  /**
   * @param PDOStatement $stmt
   * @param string $representation
   *
   * @return ModelCollection
   */
  protected function generateCollection(PDOStatement $stmt = null, $representation = Model::DEFAULT_REPRESENTATION)
  {
    $return = new ModelCollection();

    if (is_null($stmt) === true)
    {
      return $return;
    }

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      /* @var array $row */

      $return->add($this->generateModel($representation, $data));
    }

    return $return;
  }

  /**
   * @return string
   */
  protected function getModelClass()
  {
    return preg_replace('/Table$/i', '', get_class($this));
  }

  /**
   * @param string $representation
   * @param array $data         Data for the model
   *
   * @throws Exception
   */
  protected function generateModel($representation, array $data = array())
  {
    $className = $this->getModelClass();

    if (class_exists($className) === false)
    {
      throw new Exception(sprintf('Model class: %s doesn\'t exist', $className));
    }

    return new $className($representation, $data);
  }
}