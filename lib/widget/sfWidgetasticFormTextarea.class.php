<?php

/**
 * An extension of sfWidgetFormTextarea.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  widget
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
class sfWidgetasticFormTextarea extends sfWidgetFormTextarea
{
  /**
   * Constructor.
   */
  public function __construct($options = array(), $attributes = array())
  {
    if (!isset($attributes['rows']))
    {
      $attributes['rows'] = '';
    }

    if (!isset($attributes['cols']))
    {
      $attributes['cols'] = '';
    }

    parent::__construct($options, $attributes);
  }
}
