<?php

/**
 * An extension of sfValidatorDate.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  validator
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
class sfValidatornatorDate extends sfValidatorDate
{
  /**
   * Adds the following options to sfValidatorDate:
   * 
   *  * year_from:  The earliest valid year
   *  * year_to:    The latest valid year
   * 
   * Both options can be either numeric years or strings parsable by 
   * {@link strtotime()}.
   * 
   * @see sfValidatorDate
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
    
    $this->addOption('year_from');
    $this->addOption('year_to');
  }
  
  /**
   * @see sfValidatorDate
   */
  protected function doClean($value)
  {
    if (!is_null($yearFrom = $this->getOption('year_from')))
    {
      $this->setOption('min', sprintf('%d-01-01', ctype_digit($yearFrom) ? $yearFrom : date('Y', strtotime($yearFrom))));
    }
    if (!is_null($yearTo = $this->getOption('year_to')))
    {
      $this->setOption('max', sprintf('%d-12-31', ctype_digit($yearTo) ? $yearTo : date('Y', strtotime($yearTo))));
    }
    
    return parent::doClean($value);
  }
}
