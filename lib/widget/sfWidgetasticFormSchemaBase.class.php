<?php

/**
 * Base extension of sfWidgetFormSchema.
 * 
 * Extensions in this base class should NOT introduce any symfony-specific
 * behaviors or logic.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  widget
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfWidgetasticFormSchemaBase extends sfWidgetFormSchema
{
  /**
   * @see sfWidgetFormSchema
   */
  public function __construct($fields = null, $options = array(), $attributes = array(), $labels = array(), $helps = array())
  {
    parent::__construct($fields, $options, $attributes, $labels, $helps);
    
    $this->addFormFormatter('dl', new sfWidgetasticFormSchemaFormatterDefinitionList($this));
  }
  
  /**
   * @see sfWidgetFormSchema::offsetSet()
   */
  public function add($name, $widget)
  {
    $this[$name] = $widget;
  }
}
