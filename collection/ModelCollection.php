<?php

namespace Albino\Collection;

use \Albino\Model;
use \Albino\Collection;

/**
 * ModelCollection class.
 *
 * @package    Albino
 * @subpackage Collection
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 *
 * @method Model getFirst
 */
class ModelCollection extends Collection
{

  /**
   * @param Model $item
   * @param null $key
   *
   * @return ModelCollection
   */
  public function add($item, $key = null)
  {
    $this->validateIsModel($item);

    return parent::add($item, $key);
  }

  /**
   * @param mixed $item
   * @throws \Exception
   */
  protected function validateIsModel($item)
  {
    if (($item instanceof Model) === false)
    {
      throw new \Exception('Collection item should be an instance of Model');
    }
  }

  /**
   * @param string $field
   * @return array
   */
  public function getFieldValues($field)
  {
    $return = array();

    foreach ($this->data as $model)
    {
      /* @var Model $model */

      $return[] = $model->get($field);
    }

    return $return;
  }
}