<?php

require_once dirname(__FILE__) . '/DataHolder.php';
require_once dirname(__FILE__) . '/ModelRepresentation.php';

/**
 * Model class.
 *
 * @package    Albino
 * @subpackage Model
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
class Model extends DataHolder
{

  /**
   * @var string
   */
  const DEFAULT_REPRESENTATION = 'default';

  /**
   * @var array
   */
  protected $primary = array();

  /**
   * @var array
   */
  protected $representations = array();

  /**
   * @param string $representation
   * @param array $data
   */
  public function __construct($representation = self::DEFAULT_REPRESENTATION, array $data = array())
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
    $this->addRepresentation(self::DEFAULT_REPRESENTATION, new ModelRepresentation(
      ''
    ));
  }

  /**
   * @param $identifier
   * @param ModelRepresentation $representation
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
   * @param string $representation      Representation identifier
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
}