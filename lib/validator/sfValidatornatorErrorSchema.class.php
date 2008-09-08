<?php

/**
 * Extension of sfValidatorErrorSchema.
 * 
 * All symfony-specific extensions to sfValidatorErrorSchema should go in this 
 * class.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  validator
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfValidatornatorErrorSchema extends sfValidatornatorErrorSchemaBase
{
  /**
   * Returns the current error schema serialized as JSON.
   * 
   * @return  string
   */
  public function toJson()
  {
    $callable = sfConfig::get('app_sf_formtastic_plugin_json_encode_callable', 'json_encode');
    if (!is_callable($callable))
    {
      throw new RuntimeException(sprintf('The json_encoder_callable "%s" does not exist.', var_export($callable, true)));
    }
    
    // build an array of scalars
    $data = array();
    foreach ($this->globalErrors as $error)
    {
      if (!isset($data['_global']))
      {
        $data['_global'] = array();
      }
      $data['_global'][] = $error->getMessage();
    }
    foreach ($this->getNamedErrors() as $name => $error)
    {
      if (!isset($data[$name]))
      {
        $data[$name] = array();
      }
      $data[$name][] = $error->getMessage();
    }
    
    return call_user_func($callable, $data);
  }
}
