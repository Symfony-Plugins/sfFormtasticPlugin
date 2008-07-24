<?php

/**
 * A form fields-only presentation of a form.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  widget
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfWidgetasticFormSchemaFormatterNone extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat                 = '%field%%hidden_fields%',
    $helpFormat                = '',
    $errorRowFormat            = '',
    $errorListFormatInARow     = '',
    $errorRowFormatInARow      = '',
    $namedErrorRowFormatInARow = '',
    $decoratorFormat           = '%content%';
}
