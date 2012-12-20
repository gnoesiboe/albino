<?php

/**
 * Connection class.
 *
 * @package    Albino
 * @subpackage Connection
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
class Connection
{

  /**
   * @var PDO
   */
  protected $connection = null;

  /**
   * @var array
   */
  protected $options = array(
    'lazy' => true,
    'host' => null,
    'name' => null,
    'username' => null,
    'password' => null
  );

  /**
   * @var array
   */
  protected $requiredOptions = array('host', 'name', 'username', 'password');

  /**
   * Constructor
   */
  public function __construct(array $options = array())
  {
    $this->options = $this->prepareOptions($options);

    $this->validateOptions();

    if ($this->options['lazy'] === false)
    {
      $this->open();
    }
  }

  /**
   * @param array $options
   * @return array
   */
  protected function prepareOptions(array $options)
  {
    return array_merge($this->options, $options);
  }

  /**
   * @throws Exception
   */
  protected function validateOptions()
  {
    $missing = array();

    foreach ($this->requiredOptions as $key)
    {
      try
      {
        $this->validateOption($key);
      }
      catch (Exception $e)
      {
        $missing[] = $key;
      }
    }

    if (count($missing) > 0)
    {
      throw new Exception(sprintf('Missing required options: %s', implode(', ', $missing)));
    }
  }

  /**
   * @param string $key
   * @throws Exception
   */
  protected function validateOption($key)
  {
    // check if not null, empty string or empty array
    if (empty($this->options[$key]) === true)
    {
      throw new Exception(sprintf('Option: %s not specified or empty', $key));
    }
  }

  /**
   * @return bool
   */
  public function isOpened()
  {
    return $this->connection instanceof PDO;
  }

  /**
   * Opens the database connection
   */
  public function open()
  {
    $this->connection = new PDO($this->generateDsn(), $this->options['username'], $this->options['password'], array(
      Pdo::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
  }

  /**
   * @return string
   */
  protected function generateDsn()
  {
    return sprintf('mysql:host=%s;dbname=%s', $this->options['host'], $this->options['name']);
  }

  /**
   * Closes the database connection
   */
  public function close()
  {
    $this->connection = null;
  }

  /**
   * @return bool
   */
  public function isClosed()
  {
    return is_null($this->connection);
  }

  /**
   * @param string $query
   * @param array $driverOptions
   *
   * @return PDOStatement
   */
  public function prepare($query, $driverOptions = array())
  {
    if ($this->isOpened() === false)
    {
      $this->open();
    }

    return $this->connection->prepare($query, $driverOptions);
  }

  /**
   * Allowe method calls to the connection
   *
   * @param string $name
   * @param array $arguments
   *
   * @throws Exception
   */
  public function __call($name, array $arguments)
  {
    if ($this->isOpened() === true)
    {
      return $this->connection->$name($arguments);
    }

    throw new Exception(sprintf('Call to function \'%s\' that doesn not axist', $name));
  }
}