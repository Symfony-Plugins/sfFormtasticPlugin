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
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues, $taintedFiles);
    
    // replace sfValidatorErrorSchema
    $this->errorSchema = new sfValidatornatorErrorSchema($this->validatorSchema, $this->errorSchema);
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
    $clone = clone $this;
    
    if ($this->isBound())
    {
      $errorSchemaClass = get_class($this->errorSchema);
      $clone->setErrorSchema(new $errorSchemaClass($this->validatorSchema));
    }
    
    foreach ($this->getFormFieldSchema() as $name => $field)
    {
      if (!$field->isHidden())
      {
        unset($clone[$name]);
      }
    }
    
    return $clone->render();
  }
  
  /**
   * Set the error schema for this form.
   * 
   * @param   sfValidatorErrorSchema $errorSchema
   */
  public function setErrorSchema(sfValidatorErrorSchema $errorSchema)
  {
    $this->errorSchema = $errorSchema;
  }
  
  /**
   * Set an id format for all fields in this form.
   * 
   * @param   string $format
   */
  public function setIdFormat($format)
  {
    if (false !== $format && false === strpos($format, '%s'))
    {
      throw new InvalidArgumentException(sprintf('The id format must contain %%s ("%s" given)', $format));
    }
    
    $this->widgetSchema->setOption('id_format', $format);
  }
  
  /**
   * Get the id format for this form.
   * 
   * @return  string
   */
  public function getIdFormat()
  {
    return $this->widgetSchema->getOption('id_format');
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
    unset($this[$name]);
  }
  
  /**
   * @see sfWidgetSchema::setNameFormat()
   */
  public function setNameFormat($format)
  {
    $this->widgetSchema->setNameFormat($format);
  }
  
  /**
   * @see sfWidgetSchema::setLabels()
   */
  public function setLabels($labels)
  {
    $this->widgetSchema->setLabels($labels);
  }
  
  /**
   * @see sfWidgetSchema::setHelps()
   */
  public function setHelps($helps)
  {
    $this->widgetSchema->setHelps($helps);
  }
  
  /**
   * @see sfValidatorSchema::setPreValidator()
   */
  public function setPreValidator(sfValidatorBase $validator)
  {
    $this->validatorSchema->setPreValidator($validator);
  }
  
  /**
   * @see sfValidatorSchema::setPostValidator()
   */
  public function setPostValidator(sfValidatorBase $validator)
  {
    $this->validatorSchema->setPostValidator($validator);
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
