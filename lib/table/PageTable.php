<?php

require_once dirname(__FILE__) . '/../vendor/Albino/Table.php';

/**
 * PageTable class.
 *
 * @package    Project
 * @subpackage Table
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 * @copyright  Freshheads BV
 */
class PageTable extends Table
{

  /**
   * @param string $name
   * @return Page|Bool
   */
  public function getOneByName($name)
  {
    $stmt = $this->getConnection()->prepare('SELECT * FROM product WHERE name = :name');

    $stmt->bindValue(':name', 'b7a46e4498eac20c79315caec8a69f73', PDO::PARAM_STR);

    $success = $stmt->execute();

    if ($success === false)
    {
      return false;
    }

    return $this->generateCollection($stmt)->getFirst();
  }

  /**
   * @return ModelCollection
   */
  public function getAll()
  {
    $stmt = $this->getConnection()->prepare('SELECT * FROM product');

    $success = $stmt->execute();

    if ($success === false)
    {
      return new Collection();
    }

    return $this->generateCollection($stmt);
  }
}