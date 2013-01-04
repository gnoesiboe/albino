<?php

namespace Albino\Parser;

/**
 * Ini class.
 *
 * @package    Albino
 * @author     Gijs Nieuwenhuis <gijs.nieuwenhuis@freshheads.com>
 */
class Ini extends \Albino\Parser
{

  /**
   * @param string $file
   * @param string $section
   *
   * @return array
   *
   * @throws \Exception
   */
  public function parse($file, $section = null)
  {
    $data = parse_ini_file($file, true, INI_SCANNER_NORMAL);

    if (is_null($section) === false)
    {
      if (isset($data[$section]) === false)
      {
        throw new \Exception(sprintf('Section \'%s\' doesn\'t exist', $section));
      }

      $return = $data[$section];
    }
    else
    {
      $return = $data;
    }

    if (isset($return['_extends']) === true)
    {
      $return = $this->processExtends($data, $return['_extends'], $return);
    }

    unset($return['_extends']);

    return $this->toDataHolder($return);
  }

  /**
   * @param array $data
   * @param string $section
   * @param array $return
   *
   * @return array
   *
   * @throws \Exception
   */
  protected function processExtends(array $data, $section, array $return)
  {
    if (isset($data[$section]) === false)
    {
      throw new \Exception(sprintf('Section \'%s\' doesn\'t exist', $section));
    }

    $sectionData = $data[$section];

    // check if this section data actually extends another section
    if (is_array($sectionData) && isset($sectionData['_extends']))
    {
      $return = $this->processExtends($data, $sectionData['_extends'], $return);
    }

    $return = $this->arrayMergeRecursive($return, $sectionData);

    return $return;
  }
}