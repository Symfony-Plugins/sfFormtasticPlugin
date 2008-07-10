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
    $widgetSchemaClass = 'sfWidgetasticFormSchema';
}
