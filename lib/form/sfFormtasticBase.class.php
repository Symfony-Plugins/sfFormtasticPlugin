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
  static protected
    $defaultValidator = null;
  
  protected
    $localCSRFProtection  = null,
    $localCSRFSecret      = null,
    
    $validatorSchemaClass = 'sfValidatornatorSchemaBase',
    $widgetSchemaClass    = 'sfWidgetasticFormSchemaBase',
    $errorSchemaClass     = 'sfValidatornatorErrorSchemaBase',
    $formFieldSchemaClass = 'sfFormtasticFieldSchemaBase';
  
  /**
   * Set the default validator used by ->addField().
   * 
   * @param   sfValidatorBase $validator
   */
  static public function setDefaultValidator(sfValidatorBase $validator)
  {
    self::$defaultValidator = $validator;
  }
  
  /**
   * Get the default validator used by ->addField().
   * 
   * @return  sfValidatorBase
   */
  static public function getDefaultValidator()
  {
    if (is_null(self::$defaultValidator))
    {
      self::$defaultValidator = new sfValidatorPass;
    }
    
    return self::$defaultValidator;
  }
  
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
   * Add a field.
   * 
   * @param   string          $name
   * @param   sfWidget        $widget
   * @param   sfValidatorBase $validator
   */
  public function addField($name, sfWidget $widget, sfValidatorBase $validator = null)
  {
    if ($this->isBound())
    {
      throw new LogicException('Fields cannot be added to a bound form');
    }
    
    $this->widgetSchema[$name] = $widget;
    $this->validatorSchema[$name] = is_null($validator) ? clone $this->getDefaultValidator() : $validator;
    
    $this->resetFormFields();
  }
  
  /**
   * @see sfForm::offsetExists()
   */
  public function hasField($name)
  {
    return isset($this[$name]);
  }
  
  /**
   * @see sfForm::offsetGet()
   */
  public function getField($name)
  {
    return $this[$name];
  }
  
  /**
   * @see sfForm::offsetUnset()
   */
  public function removeField($name)
  {
    unset($name);
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
