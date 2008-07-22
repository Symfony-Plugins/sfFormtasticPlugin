<?php

/**
 * Generation logic for a sfFormtastic YAML validator.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  generator
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
class sfFormtasticGeneratorValidator extends sfFormtasticGeneratorBase
{
  protected
    $class    = null,
    $options  = array(),
    $messages = array();
  
  /**
   * Constructor.
   */
  public function __construct($class, $config)
  {
    $this->initialize($class, $config);
  }
  
  /**
   * Initialize validator configuration.
   * 
   * @param   string  $class
   * @param   array   $config
   */
  public function initialize($class, $config = array())
  {
    $this->class = $class;
    
    foreach ($config as $key => $value)
    {
      if (in_array($key, array('invalid', 'error')))
      {
        $this->messages['invalid'] = $value;
      }
      elseif (preg_match('/^(\w+)_error$/', $key, $match))
      {
        $this->messages[$match[1]] = $value;
      }
      else
      {
        $this->options[$key] = $value;
      }
    }
  }
  
  /**
   * Set validator required option.
   * 
   * @param   boolean $required
   * @param   string  $message
   */
  public function setRequired($required, $message = null)
  {
    if ($required)
    {
      unset($this->options['required']);
      if ($message)
      {
        $this->messages['required'] = $message;
      }
    }
    else
    {
      $this->options['required'] = false;
      unset($this->messages['required']);
    }
  }
  
  /**
   * Generate a validator instantiation.
   * 
   * @return  string
   */
  public function generate()
  {
    if ($this->messages)
    {
      return sprintf('new %s(%s, %s)', $this->class, $this->varExport($this->options), $this->varExport($this->messages));
    }
    elseif ($this->options)
    {
      return sprintf('new %s(%s)', $this->class, $this->varExport($this->options));
    }
    else
    {
      return 'new '.$this->class;
    }
  }
}
