<?php

/**
 * An extension of sfForm.
 * 
 * All symfony-specific extensions to sfForm should go in this class.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  form
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfFormtastic extends sfFormtasticBase
{
  protected
    $validatorSchemaClass = 'sfValidatornatorSchema',
    $widgetSchemaClass    = 'sfWidgetasticFormSchema',
    $errorSchemaClass     = 'sfValidatornatorErrorSchema',
    $formFieldSchemaClass = 'sfFormtasticFieldSchema';
  
  /**
   * @see sfForm
   */
  public function setValidatorSchema(sfValidatorSchema $validatorSchema)
  {
    $request = sfContext::getInstance()->getRequest();
    
    if ($request->isXmlHttpRequest() && 
        $request->isMethod('post') && 
        preg_match('/(Konqueror|Safari|KHTML)/', $_SERVER['HTTP_USER_AGENT']))
    {
      // prototype.js adds an underscore parameter to POST requests for 
      // certain web browsers
      $validatorSchema['_'] = new sfValidatorPass;
    }
    
    parent::setValidatorSchema($validatorSchema);
  }
}
