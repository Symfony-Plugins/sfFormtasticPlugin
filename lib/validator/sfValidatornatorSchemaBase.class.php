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
   * @see sfValidatorBase::offsetSet()
   */
  public function add($name, $validator)
  {
    $this[$name] = $validator;
  }
}
