<?php

namespace Albino;

use Albino\DataHolder;

/**
 * Parser class.
 *
 * @package    Albino
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
abstract class Parser
{

  /**
   * @abstract
   * @param string $file
   * @return array
   */
  abstract public function parse($file);

  /**
   * @param array $firstArray
   * @param array $secondArray
   *
   * @return array
   */
  protected function arrayMergeRecursive($firstArray, $secondArray)
  {
    if (is_array($firstArray) && is_array($secondArray))
    {
      foreach ($secondArray as $key => $value)
      {
        if (isset($firstArray[$key]))
        {
          $firstArray[$key] = $this->arrayMergeRecursive($firstArray[$key], $value);
        }
        else
        {
          if ($key === 0)
          {
            $firstArray = array(0 => $this->arrayMergeRecursive($firstArray, $value));
          }
          else
          {
            $firstArray[$key] = $value;
          }
        }
      }
    }
    else
    {
      $firstArray = $secondArray;
    }

    return $firstArray;
  }

  /**
   * @param array $data
   * @return DataHolder
   */
  protected function toDataHolder(array $data)
  {
    $dataHolder = new DataHolder();

    foreach ($data as $key => $value)
    {
      $dotPos = strpos($key, '.');
      if ($dotPos !== false)
      {
        $dataHolder->set(substr($key, 0, $dotPos), $this->toDataHolder(array(
          substr($key, $dotPos + 1) => $value
        )));
      }
      else
      {
        $dataHolder->set($key, $value);
      }
    }

    return $dataHolder;
  }
}