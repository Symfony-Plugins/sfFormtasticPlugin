<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(4, new lime_output_color);

class FormattedForm extends sfFormtastic
{
  public function configure()
  {
    $this->setWidgets(array('name' => new sfWidgetFormInput));
    $this->setValidators(array('name' => new sfValidatorPass));
  }
}

$form = new FormattedForm;
$widgetSchema = $form->getWidgetSchema();

$widgetSchema->setFormFormatterName('table');
$t->like($form->render(), '/\<tr\>/', '"sfWidgetasticFormSchemaFormatterTable" renders <tr> tags');

$widgetSchema->setFormFormatterName('list');
$t->like($form->render(), '/\<li\>/', '"sfWidgetasticFormSchemaFormatterList" renders <li> tags');

$widgetSchema->setFormFormatterName('dl');
$t->like($form->render(), '/\<dt\>/', '"sfWidgetasticFormSchemaFormatterDefinitionList" renders <dt> tags');

$widgetSchema->setFormFormatterName('none');
$t->is($form->render(), '<input type="text" name="name" id="name" />', '"sfWidgetasticFormSchemaFormatterNone" renders no non-form tags');
