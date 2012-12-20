<?php

require_once dirname(__FILE__) . '/DataHolder.php';

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
   * @var array
   */
  protected $primary = array();

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    $data = $this->prepareData($data);

    parent::__construct($data);
  }

  /**
   * @param array $data
   * @return array
   */
  protected function prepareData(array $data)
  {
    return $data;
  }
}