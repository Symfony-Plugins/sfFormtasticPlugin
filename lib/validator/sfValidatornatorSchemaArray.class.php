<?php

/**
 * Run a single validator across every value in an array.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  validator
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
class sfValidatornatorSchemaArray extends sfValidatorSchema
{
  /**
   * Constructor.
   * 
   * @param   sfValidatorBase $validator A validator to clean every value in the array of values
   * @param   array           $options
   * @param   array           $messages
   * 
   * @see sfValidatorSchema
   */
  public function __construct(sfValidatorBase $validator, $options = array(), $messages = array())
  {
    $this->addOption('validator', $validator);
    
    parent::__construct(null, $options, $messages);
  }
  
  /**
   * @see sfValidatorBase
   */
  protected function doClean($values)
  {
    if (is_null($values))
    {
      $values = array();
    }
    
    if (!is_array($values))
    {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }
    
    $clean = array();
    
    foreach ($values as $i => $value)
    {
      $validator = $this->getOption('validator');
      $validator = clone $validator;
      
      $clean[$i] = $validator->clean($value);
    }
    
    return $clean;
  }
}
