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
   * Bind parameters from the symfony request object, based on name format.
   * 
   * @throws LogicException If the name format is not recognized
   */
  public function bindRequestParameters()
  {
    $request = sfContext::getInstance()->getRequest();

    if ('%s' == $nameFormat = $this->widgetSchema->getNameFormat())
    {
      $this->bind($request->isMethod('post') ? $request->getPostParameters() : $request->getGetParameters(), $request->getFiles());
    }
    elseif ('[%s]' == substr($nameFormat, -4))
    {
      $this->bind($request->getParameter(substr($nameFormat, 0, -4)), $request->getFiles());
    }
    else
    {
      throw new LogicException(sprintf('%s cannot understand the name format "%s"', __METHOD__, $nameFormat));
    }
  }

  /**
   * @see sfForm
   */
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues, $taintedFiles);

    if (!$this->isValid())
    {
      $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();

      $dispatcher->notify(new sfEvent($this, 'application.log', array('Form validation failed: '.$this->errorSchema->getMessage())));
      $dispatcher->notify(new sfEvent($this, 'form.validation_failure'));
    }
  }

  /**
   * @see sfForm
   */
  public function setValidatorSchema(sfValidatorSchema $validatorSchema)
  {
    if (sfProjectConfiguration::getActive() instanceof sfApplicationConfiguration)
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
    }

    parent::setValidatorSchema($validatorSchema);
  }
}
