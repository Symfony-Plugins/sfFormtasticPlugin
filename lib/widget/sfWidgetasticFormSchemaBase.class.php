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

    $this->addFormFormatter('table', new sfWidgetasticFormSchemaFormatterTable($this));
    $this->addFormFormatter('list', new sfWidgetasticFormSchemaFormatterList($this));
    $this->addFormFormatter('dl', new sfWidgetasticFormSchemaFormatterDefinitionList($this));
    $this->addFormFormatter('none', new sfWidgetasticFormSchemaFormatterNone($this));
  }

  /**
   * @see sfWidgetFormSchema::offsetExists()
   */
  public function has($name)
  {
    return isset($this[$name]);
  }

  /**
   * @see sfWidgetFormSchema::offsetGet()
   */
  public function get($name)
  {
    return $this[$name];
  }

  /**
   * @see sfWidgetFormSchema::offsetSet()
   */
  public function add($name, sfWidget $widget)
  {
    $this[$name] = $widget;
  }

  /**
   * @see sfWidgetFormSchema::offsetUnset()
   */
  public function remove($name)
  {
    unset($this[$name]);
  }
}
