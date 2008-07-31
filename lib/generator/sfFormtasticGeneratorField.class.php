<?php

/**
 * Generation logic for sfFormtastic YAML form fields.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  generator
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
class sfFormtasticGeneratorField extends sfFormtasticGeneratorBase
{
  static protected
    $widgetAliasMap = array(),
    $defaultWidgetAliasMap = array(
      'date'      => 'sfWidgetasticFormDate',
      'datetime'  => 'sfWidgetasticFormDateTime',
      'timestamp' => 'sfWidgetasticFormDateTime',
      'input'     => 'sfWidgetasticFormInput',
      'checkbox'  => 'sfWidgetasticFormInputCheckbox',
      'file'      => 'sfWidgetasticFormInputFile',
      'upload'    => 'sfWidgetasticFormInputFile',
      'hidden'    => 'sfWidgetasticFormInputHidden',
      'password'  => 'sfWidgetasticFormInputPassword',
      'select'    => 'sfWidgetasticFormSelect',
      'dropdown'  => 'sfWidgetasticFormSelect',
      'many'      => 'sfWidgetasticFormSelectMany',
      'radio'     => 'sfWidgetasticFormSelectRadio',
      'textarea'  => 'sfWidgetasticFormTextarea',
      'time'      => 'sfWidgetasticFormTime',
    ),
    $sisterValidatorMap = array(
      'sfWidgetasticFormDate'        => 'sfValidatornatorDate',
      'sfWidgetasticFormDateTime'    => 'sfValidatornatorDateTime',
      'sfWidgetasticFormInputFile'   => 'sfValidatornatorFile',
      'sfWidgetasticFormSelect'      => 'sfValidatornatorChoice',
      'sfWidgetasticFormSelectMany'  => 'sfValidatornatorChoiceMany',
      'sfWidgetasticFormSelectRadio' => 'sfValidatornatorChoice',
      'sfWidgetasticFormTime'        => 'sfValidatornatorTime',
    );
  
  protected
    $name             = null,
    $help             = null,
    $label            = null,
    $widgetClass      = 'sfWidgetFormInput',
    $widgetOptions    = array(),
    $widgetAttributes = array(),
    $required         = false,
    $requiredMessage  = null,
    $validators       = array();
  
  /**
   * Constructor.
   * 
   * @param   string  $name
   * @param   array   $config
   */
  public function __construct($name, $config = array())
  {
    $this->initialize($name, $config);
  }
  
  /**
   * Initialize form field configuration.
   * 
   * @param   string  $name
   * @param   array   $param
   */
  public function initialize($name, $config = array())
  {
    // field name
    $this->name = $name;
    
    // field label
    if (isset($config['label']))
    {
      $this->label = $config['label'];
      unset($config['label']);
    }
    
    // help message
    if (isset($config['help']))
    {
      $this->help = $config['help'];
      unset($config['help']);
    }
    
    // determine widget class
    if (isset($config['type']))
    {
      $this->widgetClass = $this->translateTypeToWidgetClass($config['type']);
      unset($config['type']);
    }
    elseif ($widgetClass = $this->translateNameToWidgetClass($this->name))
    {
      $this->widgetClass = $widgetClass;
    }
    
    // capture field required configuration
    if (isset($config['required']) && $config['required'])
    {
      $this->required = true;
      if (isset($config['required']['msg']))
      {
        $this->requiredMessage = $config['required']['msg'];
      }
    }
    unset($config['required']);
    
    // add configured validators
    $baseRc = new ReflectionClass('sfValidatorBase');
    foreach ($config as $validator => $params)
    {
      if (class_exists($validator))
      {
        $rc = new ReflectionClass($validator);
        if ($rc->isSubClassOf($baseRc))
        {
          $this->validators[] = new sfFormtasticGeneratorValidator($validator, $params ? $params : array());
          unset($config[$validator]);
        }
      }
    }
    
    // remaining config are either widget options or attributes
    if ($config)
    {
      $widgetClass = $this->widgetClass;
      try
      {
        $tmpWidget = new $widgetClass;
      }
      catch (RuntimeException $e)
      {
        $tmpOptions = array();
        preg_match_all('/\'(\w+)\'/', $e->getMessage(), $matches);
        foreach ($matches[1] as $match)
        {
          $tmpOptions[$match] = null;
        }
        $tmpWidget = new $widgetClass($tmpOptions);
      }
      
      foreach ($config as $key => $value)
      {
        try
        {
          $tmpWidget->setOption($key, $value);
          
          $this->widgetOptions[$key] = $value;
        }
        catch (InvalidArgumentException $e)
        {
          $this->widgetAttributes[$key] = $value;
        }
      }
    }
    
    // some widget classes have sister validator classes
    if ($validator = $this->getSisterValidator())
    {
      $this->validators[] = $validator;
    }
    
    // if there are no validators, guess based on field name
    if (!$this->validators)
    {
      if ('id' == $this->name)
      {
        $this->validators[] = new sfFormtasticGeneratorValidator('sfValidatorInteger');
      }
      elseif (false !== stripos($this->name, 'email'))
      {
        $this->validators[] = new sfFormtasticGeneratorValidator('sfValidatorEmail');
      }
    }
  }
  
  /**
   * Generate a widget instantiation for this field.
   * 
   * @return  string
   */
  public function generateWidgetInstantiation()
  {
    if ($this->widgetAttributes)
    {
      return sprintf('new %s(%s, %s)', $this->widgetClass, $this->varExport($this->widgetOptions), $this->varExport($this->widgetAttributes));
    }
    elseif ($this->widgetOptions)
    {
      return sprintf('new %s(%s)', $this->widgetClass, $this->varExport($this->widgetOptions));
    }
    else
    {
      return 'new '.$this->widgetClass;
    }
  }
  
  /**
   * Generate a validator instantiation for this field.
   * 
   * @return  string
   */
  public function generateValidatorInstantiation()
  {
    if (count($this->validators) > 1)
    {
      $data = array();
      
      $data[] = 'new sfValidatorAnd(array(';
      foreach ($this->validators as $validator)
      {
        $validator->setRequired(false);
        $data[] = '        '.$validator->generate().',';
      }
      
      if ($this->requiredMessage)
      {
        $data[] = sprintf('      ), array(), %s)', $this->varExport(array('required' => $this->requiredMessage)));
      }
      elseif (!$this->required)
      {
        $data[] = sprintf('      ), %s)', $this->varExport(array('required' => false)));
      }
      else
      {
        $data[] = '      ))';
      }
      
      return join("\n", $data);
    }
    elseif ($this->validators)
    {
      $validator = $this->validators[0];
      $validator->setRequired($this->required, $this->requiredMessage);
      
      return $validator->generate();
    }
    else
    {
      // fallback to simple required validator
      if ($this->required)
      {
        $validator = new sfFormtasticGeneratorValidator('sfValidatorString');
        $validator->setRequired($this->required, $this->requiredMessage);
      }
      else
      {
        $validator = new sfFormtasticGeneratorValidator('sfValidatorPass');
      }
      
      return $validator->generate();
    }
  }
  
  /**
   * Get the field name.
   * 
   * @return  string
   */
  public function getName()
  {
    return $this->name;
  }
  
  /**
   * Whether this field has a custom label.
   * 
   * @return  boolean
   */
  public function hasLabel()
  {
    return !is_null($this->label);
  }
  
  /**
   * Get custom label for this field.
   * 
   * @return  string
   */
  public function getLabel()
  {
    return $this->label;
  }
  
  /**
   * Whether this field has a help message.
   * 
   * @return  boolean
   */
  public function hasHelp()
  {
    return !is_null($this->help);
  }
  
  /**
   * Get help message for this field.
   * 
   * @return  string
   */
  public function getHelp()
  {
    return $this->help;
  }
  
  /**
   * Get any prevalidator required by this field.
   * 
   * @return  sfFormtasticGeneratorValidator
   */
  public function getPreValidator()
  {
  }
  
  /**
   * Get any postvalidator required by this field.
   * 
   * @return  sfFormtasticGeneratorValidator
   */
  public function getPostValidator()
  {
  }
  
  /**
   * Translate a shorthand type value to a widget class.
   * 
   * @throws  InvalidArgumentException  If widget class could not be found or 
   *                                    does not extend sfWidget
   * 
   * @param   string $type
   * 
   * @return  string
   */
  protected function translateTypeToWidgetClass($type)
  {
    if (!self::$widgetAliasMap)
    {
      self::$widgetAliasMap = array_merge(self::$defaultWidgetAliasMap, sfConfig::get('app_sf_formtastic_plugin_widget_alias_map', array()));
    }
    
    $widgetClass = isset(self::$widgetAliasMap[$type]) ? self::$widgetAliasMap[$type] : $type;
    if (class_exists($widgetClass))
    {
      $rc = new ReflectionClass($widgetClass);
      if (!$rc->isSubclassOf(new ReflectionClass('sfWidget')))
      {
        throw new InvalidArgumentException(sprintf('The class "%s" is not a subclass of sfWidget', $widgetClass));
      }
    }
    else
    {
      throw new InvalidArgumentException(sprintf('The class "%s" could not be found', $widgetClass));
    }
    
    return $widgetClass;
  }
  
  /**
   * Translate a field name to widget class.
   * 
   * @param   string $name
   * 
   * @return  string
   */
  protected function translateNameToWidgetClass($name)
  {
    if ('id' == $name)
    {
      return 'sfWidgetFormInputHidden';
    }
    elseif (false !== strpos($name, 'password'))
    {
      return 'sfWidgetFormInputPassword';
    }
    elseif ('_at' == substr($name, -3))
    {
      return 'sfWidgetFormInputDateTime';
    }
    elseif ('_on' == substr($name, -3))
    {
      return 'sfWidgetFormInputDate';
    }
  }
  
  /**
   * Get a widget class' sister validator.
   * 
   * @return  sfFormtasticGeneratorValidator
   */
  protected function getSisterValidator()
  {
    if (isset(self::$sisterValidatorMap[$this->widgetClass]))
    {
      $validator = self::$sisterValidatorMap[$this->widgetClass];
      $params = array();
      
      // some widget options need to be copied to the validator
      if (false !== strpos($this->widgetClass, 'Select') && isset($this->widgetOptions['choices']))
      {
        $params['choices'] = $this->widgetOptions['choices'];
      }
      elseif (false !== strpos($this->widgetClass, 'Date'))
      {
        if (isset($this->widgetOptions['year_from']))
        {
          $params['year_from'] = $this->widgetOptions['year_from'];
        }
        
        if (isset($this->widgetOptions['year_to']))
        {
          $params['year_to'] = $this->widgetOptions['year_to'];
        }
      }
      elseif (false !== strpos($this->widgetClass, 'File') && preg_match('/(img|image|pic|avatar)/i', $this->name))
      {
        $params['mime_types'] = 'web_images';
      }
      
      return new sfFormtasticGeneratorValidator($validator, $params);
    }
  }
}
