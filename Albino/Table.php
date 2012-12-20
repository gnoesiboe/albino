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
   * @return ModelCollection
   */
  protected function generateCollection(PDOStatement $stmt)
  {
    $return = new ModelCollection();

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      /* @var array $row */

      $return->add($this->generateModel('Page', $data));
    }

    return $return;
  }

  /**
   * @param string $name        Model class name
   * @param array $data         Data for the model
   */
  protected function generateModel($name, array $data = array())
  {
    return new $name($data);
  }
}