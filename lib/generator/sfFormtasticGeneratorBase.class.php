<?php

/**
 * Common logic for generator classes.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  generator
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
abstract class sfFormtasticGeneratorBase
{
  /**
   * Export and clean up.
   * 
   * @param   mixed $var
   * 
   * @return  string
   */
  protected function varExport($var)
  {
    $export = var_export($var, true);
    
    if (0 === strpos($export, 'array ('))
    {
      $export = preg_replace('/\s+/', ' ', $export);
      $export = str_replace(array(' ( ', ', )'), array('(', ')'), $export);
      if (false === strpos('\' => ', $export))
      {
        $export = preg_replace('/\d+ => /', '', $export);
      }
    }
    
    return $export;
  }
}
