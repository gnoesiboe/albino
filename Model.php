<?php

namespace Albino;

/**
 * Model class.
 *
 * @package    Albino
 * @subpackage Model
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
abstract class Model extends \Albino\DataHolder
{

  /**
   * @var string
   */
  const DEFAULT_REPRESENTATION = 'default';

  /**
   * @var array
   */
  protected $representations = array();

  /**
   * @param array $data
   * @param string $representation
   */
  public function __construct(array $data = array(), $representation = self::DEFAULT_REPRESENTATION)
  {
    $this->configureRepresentations();

    $data = $this->prepareData($representation, $data);

    parent::__construct($data);
  }

  /**
   * Configures this model's representations
   */
  protected function configureRepresentations()
  {
    //@todo implement
    $this->addRepresentation(self::DEFAULT_REPRESENTATION, new ModelRepresentation());
  }

  /**
   * @param $identifier
   * @param \Albino\ModelRepresentation $representation
   */
  public function addRepresentation($identifier, ModelRepresentation $representation)
  {
    $this->representations[$identifier] = $representation;
  }

  /**
   * @param string $representation
   * @param array $data
   *
   * @return array
   */
  protected function prepareData($representation, array $data)
  {
    if (count($data) > 0)
    {
      $data = $this->applyRepresentation($representation, $data);
    }

    return $data;
  }

  /**
   * @param string $representation Representation identifier
   * @param array $data
   */
  protected function applyRepresentation($representation, array $data)
  {
    $representation = $this->getRepresentation($representation);

    return $data;
  }

  protected function getRepresentation($identifier)
  {
    //@todo
  }

  /**
   * @param string $name
   * @return Table
   */
  protected function getTable($name)
  {
    return DatabaseManager::getInstance()->getTable($name);
  }
}