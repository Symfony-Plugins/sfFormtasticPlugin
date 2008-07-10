<?php

/**
 * Presents a form as a definition list.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  widget
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfWidgetasticFormSchemaFormatterDefinitionList extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat        = '<dt>%label%</dt><dd>%error%%field%%help%%hidden_fields%</dd>',
    $errorRowFormat   = '<li>%errors%</li>',
    $helpFormat       = '<br />%help%',
    $decoratorFormat  = '<dl>%content%</dl>';
}
