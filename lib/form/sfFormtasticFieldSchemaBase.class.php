<?php

/**
 * Base class for sfFormtasticFieldSchema.
 * 
 * Extensions in this base class should NOT introduce any symfony-specific
 * behaviors or logic.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  form
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfFormtasticFieldSchemaBase extends sfFormFieldSchema
{
  /**
   * @return array All hidden form fields
   */
  public function getHiddenFields()
  {
    $hiddenFields = array();
    foreach ($this as $field)
    {
      if ($field->isHidden())
      {
        $hiddenFields[] = $field;
      }
    }
    
    return $hiddenFields;
  }
}
