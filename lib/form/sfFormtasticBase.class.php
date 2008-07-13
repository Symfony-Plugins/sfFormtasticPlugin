<?php

/**
 * Base class for sfFormtastic.
 * 
 * Extensions in this base class should NOT introduce any symfony-specific
 * behaviors or logic.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  form
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfFormtasticBase extends sfForm
{
  protected
    $localCSRFProtection  = null,
    $localCSRFSecret      = null,
    
    $validatorSchemaClass = 'sfValidatornatorSchemaBase',
    $widgetSchemaClass    = 'sfWidgetasticFormSchemaBase',
    $errorSchemaClass     = 'sfValidatornatorErrorSchemaBase',
    $formFieldSchemaClass = 'sfFormtasticFieldSchemaBase';
  
  /**
   * @see sfForm
   */
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    $this->setDefaults($defaults);
    $this->options = $options;
    
    $validatorSchemaClass = $this->validatorSchemaClass;
    $widgetSchemaClass    = $this->widgetSchemaClass;
    $errorSchemaClass     = $this->errorSchemaClass;
    
    $this->validatorSchema = new $validatorSchemaClass;
    $this->widgetSchema    = new $widgetSchemaClass;
    $this->errorSchema     = new $errorSchemaClass($this->validatorSchema);
    
    $this->setup();
    $this->configure();
    
    $this->addCSRFProtection($CSRFSecret);
    $this->resetFormFields();
    
    // store local CSRF flag and secret
    $this->localCSRFSecret = $CSRFSecret;
    $this->localCSRFProtection = $this->isCSRFProtected();
  }
  
  /**
   * @see sfForm
   */
  public function setWidgets(array $widgets)
  {
    $widgetSchemaClass = $this->widgetSchemaClass;
    $this->setWidgetSchema(new $widgetSchemaClass($widgets));
  }
  
  /**
   * @see sfForm
   */
  public function getFormFieldSchema()
  {
    if (is_null($this->formFieldSchema))
    {
      $formFieldSchemaClass = $this->formFieldSchemaClass;
      $this->formFieldSchema = new $formFieldSchemaClass($this->widgetSchema, null, null, $this->isBound ? $this->taintedValues : $this->defaults, $this->errorSchema);
    }
    
    return $this->formFieldSchema;
  }
  
  /**
   * @see sfForm
   */
  public function render($attributes = array())
  {
    $this->checkCSRFField();
    
    return parent::render($attributes);
  }
  
  /**
   * Render all hidden fields.
   * 
   * @return  string
   */
  public function renderHiddenFields()
  {
    $this->checkCSRFField();
    
    $rendered = array();
    foreach ($this->getFormFieldSchema()->getHiddenFields() as $field)
    {
      $rendered[] = $field->render();
    }
    
    return join("\n", $rendered);
  }
  
  /**
   * Make sure the CSRF field is there.
   */
  protected function checkCSRFField()
  {
    if ($this->localCSRFProtection && !$this->isCSRFProtected())
    {
      $this->addCSRFProtection($this->localCSRFSecret);
    }
  }
}
