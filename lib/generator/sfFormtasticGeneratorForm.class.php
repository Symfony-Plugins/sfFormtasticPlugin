<?php

/**
 * Generation logic for a single sfFormtastic YAML form.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  generator
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
class sfFormtasticGeneratorForm extends sfFormtasticGeneratorBase
{
  protected
    $class          = null,
    $extendsClass   = 'sfForm',
    $nameFormat     = null,
    $preValidators  = array(),
    $postValidators = array(),
    $fields         = array();
  
  /**
   * Constructor.
   * 
   * @param   string $class
   * @param   array $config
   */
  public function __construct($class, $config = array())
  {
    $this->initialize($class, $config);
  }
  
  /**
   * Initialize generation of a YAML form class.
   * 
   * @param   string  $class
   * @param   array   $config
   */
  public function initialize($class, $config = array())
  {
    // form level configuration
    $this->class = $class;
    
    if (isset($config['extends']))
    {
      $this->setExtendsClass($config['extends']);
    }
    
    if (isset($config['name_format']))
    {
      $this->nameFormat = $config['name_format'];
    }
    
    // form fields
    foreach ($config['fields'] as $name => $params)
    {
      $field = new sfFormtasticGeneratorField($name, $params);
      
      if ($preValidator = $field->getPreValidator())
      {
        $this->preValidators[] = $preValidator;
      }
      if ($postValidator = $field->getPostValidator())
      {
        $this->postValidators[] = $postValidator;
      }
      
      $this->fields[] = $field;
    }
  }
  
  /**
   * Generate a form class.
   * 
   * @return  string
   */
  public function generate()
  {
    $data = array();
    
    $data[] = '/**';
    $data[] = ' * '.$this->class;
    $data[] = ' * ';
    $data[] = ' * @package    symfony';
    $data[] = ' * @subpackage form';
    $data[] = ' * @author     auto-generated from YAML';
    $data[] = ' * @version    SVN: $Id$';
    $data[] = ' */';
    $data[] = sprintf('class %s extends %s', $this->class, $this->extendsClass);
    $data[] = '{';
    $data[] = '  /**';
    $data[] = '   * @see sfForm';
    $data[] = '   */';
    $data[] = '  public function configure()';
    $data[] = '  {';
    
    if ('sfForm' != $this->extendsClass)
    {
      $data[] = '    parent::configure();';
      $data[] = '';
    }
    
    $data[] = $this->generateSetWidgetsCall();
    $data[] = $this->generateSetValidatorsCall();
    
    if ($labels = $this->generateSetLabelsCall())
    {
      $data[] = $labels;
    }
    if ($helps = $this->generateSetHelpsCall())
    {
      $data[] = $helps;
    }
    if ($nameFormat = $this->generateSetNameFormatCall())
    {
      $data[] = $nameFormat;
    }
    if ($preValidator = $this->generateSetPreValidatorCall())
    {
      $data[] = $preValidator;
    }
    if ($postValidator = $this->generateSetPostValidatorCall())
    {
      $data[] = $postValidator;
    }
    
    $data[] = '  }';
    $data[] = '}';
    $data[] = '';
    
    return join("\n", $data);
  }
  
  /**
   * Generate a call to ->setWidgets().
   * 
   * @return string
   */
  protected function generateSetWidgetsCall()
  {
    $widgets = array();
    foreach ($this->fields as $field)
    {
      $widgets[] = sprintf('      %s => %s,', $this->varExport($field->getName()), $field->generateWidgetInstantiation());
    }
    
    $data = array();
    if ($widgets)
    {
      $data[] = '    $this->setWidgets(array(';
      $data[] = join("\n", $widgets);
      $data[] = '    ));';
      $data[] = '';
    }
    
    return join("\n", $data);
  }
  
  /**
   * Generate a call to ->setValidators().
   * 
   * @return string
   */
  protected function generateSetValidatorsCall()
  {
    $validators = array();
    foreach ($this->fields as $field)
    {
      $validators[] = sprintf('      %s => %s,', $this->varExport($field->getName()), $field->generateValidatorInstantiation());
    }
    
    $data = array();
    if ($validators)
    {
      $data[] = '    $this->setValidators(array(';
      $data[] = join("\n", $validators);
      $data[] = '    ));';
      $data[] = '';
    }
    
    return join("\n", $data);
  }
  
  /**
   * Generate a call to ->setLabels().
   * 
   * @return string
   */
  protected function generateSetLabelsCall()
  {
    $labels = array();
    foreach ($this->fields as $field)
    {
      if ($field->hasLabel())
      {
        $labels[] = sprintf('      %s => %s,', $this->varExport($field->getName()), $this->varExport($field->getLabel()));
      }
    }
    
    $data = array();
    if ($labels)
    {
      $data[] = '    $this->widgetSchema->setLabels(array(';
      $data[] = join("\n", $labels);
      $data[] = '    ));';
      $data[] = '';
    }
    
    return join("\n", $data);
  }
  
  /**
   * Generate a call to ->setHelps().
   * 
   * @return string
   */
  protected function generateSetHelpsCall()
  {
    $helps = array();
    foreach ($this->fields as $field)
    {
      if ($field->hasHelp())
      {
        $helps[] = sprintf('      %s => %s,', $this->varExport($field->getName()), $this->varExport($field->getHelp()));
      }
    }
    
    $data = array();
    if ($helps)
    {
      $data[] = '    $this->widgetSchema->setHelps(array(';
      $data[] = join("\n", $helps);
      $data[] = '    ));';
      $data[] = '';
    }
    
    return join("\n", $data);
  }
  
  /**
   * Generate a call to ->setNameFormat().
   * 
   * @return string
   */
  protected function generateSetNameFormatCall()
  {
    $data = null;
    if (!is_null($this->nameFormat))
    {
      $data = sprintf('    $this->widgetSchema->setNameFormat(%s);', $this->varExport($this->nameFormat))."\n";
    }
    
    return $data;
  }
  
  /**
   * Generate a call to ->setPreValidator().
   * 
   * @return string
   */
  protected function generateSetPreValidatorCall()
  {
    $data = array();
    if (count($this->preValidators) > 1)
    {
      $data[] = '    $this->validatorSchema->setPreValidator(new sfValidatorAnd(array(';
      foreach ($this->preValidators as $validator)
      {
        $data[] = sprintf('      new %s,', $validator);
      }
      $data[] = '    )));';
      $data[] = '';
    }
    elseif ($this->preValidators)
    {
      $data[] = sprintf('    $this->validatorSchema->setPreValidator(new %s);', $this->preValidators[0]);
      $data[] = '';
    }
    
    return join("\n", $data);
  }
  
  /**
   * Generate a call to ->setPostValidator().
   * 
   * @return string
   */
  protected function generateSetPostValidatorCall()
  {
    $data = array();
    if (count($this->postValidators) > 1)
    {
      $data[] = '    $this->validatorSchema->setPostValidator(new sfValidatorAnd(array(';
      foreach ($this->postValidators as $validator)
      {
        $data[] = sprintf('      new %s,', $validator);
      }
      $data[] = '    )));';
      $data[] = '';
    }
    elseif ($this->postValidators)
    {
      $data[] = sprintf('    $this->validatorSchema->setPostValidator(new %s);', $this->postValidators[0]);
      $data[] = '';
    }
    
    return join("\n", $data);
  }
  
  /**
   * Set the form class this class extends.
   * 
   * @param   string $class
   * 
   * @throws  InvalidArgumentException  If supplied class could not be found 
   *                                    or does not extend sfForm
   */
  protected function setExtendsClass($class)
  {
    if (class_exists($class))
    {
      $rc = new ReflectionClass($class);
      if ($rc->isSubclassOf(new ReflectionClass('sfForm')))
      {
        $this->extendsClass = $class;
      }
      else
      {
        throw new InvalidArgumentException(sprintf('The class "%s" is not an extension of sfForm', $class));
      }
    }
    else
    {
      throw new InvalidArgumentException(sprintf('The class "%s" could not be found', $class));
    }
  }
}
