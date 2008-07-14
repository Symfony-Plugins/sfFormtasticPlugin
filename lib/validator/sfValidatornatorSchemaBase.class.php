<?php

/**
 * Base extension of sfValidatorSchema.
 * 
 * Extensions in this base class should NOT introduce any symfony-specific
 * behaviors or logic.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  validator
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfValidatornatorSchemaBase extends sfValidatorSchema
{
  /**
   * @see sfValidatorSchema::offsetExists()
   */
  public function has($name)
  {
    return isset($this[$name]);
  }
  
  /**
   * @see sfValidatorSchema::offsetGet()
   */
  public function get($name)
  {
    return $this[$name];
  }
  
  /**
   * @see sfValidatorSchema::offsetSet()
   */
  public function add($name, sfValidatorBase $validator)
  {
    $this[$name] = $validator;
  }
  
  /**
   * @see sfValidatorSchema::offsetUnset()
   */
  public function remove($name)
  {
    unset($this[$name]);
  }
}
