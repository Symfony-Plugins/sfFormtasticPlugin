<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(11, new lime_output_color);

$autoload = sfSimpleAutoload::getInstance();
$autoload->addDirectory(dirname(__FILE__).'/../../lib');
$autoload->register();

sfForm::enableCSRFProtection('secretastic');

$form = new sfFormtasticBase;

$t->isa_ok($form->getValidatorSchema(), 'sfValidatornatorSchemaBase', '->getValidatorSchema() returns overloaded class');
$t->isa_ok($form->getWidgetSchema(), 'sfWidgetasticFormSchemaBase', '->getWidgetSchema() returns overloaded class');
$t->isa_ok($form->getErrorSchema(), 'sfValidatornatorErrorSchemaBase', '->getErrorSchema() returns overloaded class');
$t->isa_ok($form->getFormFieldSchema(), 'sfFormtasticFieldSchemaBase', '->getFormFieldSchema() returns overloaded class');

$t->like($form->renderHiddenFields(), '/_csrf_token/', '->renderHiddenFields() renders CSRF token');
$form->setWidgets(array('email' => new sfWidgetFormInput));
$form->setValidators(array('email' => new sfValidatorEmail));
$t->like($form->renderHiddenFields(), '/_csrf_token/', '->renderHiddenFields() renders CSRF token after form has been modified');

$widgetSchema = $form->getWidgetSchema();
$validatorSchema = $form->getValidatorSchema();

$form->addField('name', new sfWidgetFormInput);
$t->ok(isset($form['name']), '->addField() sets a form field');
$t->ok(isset($widgetSchema['name']), '->addField() sets a widget');
$t->ok(isset($validatorSchema['name']), '->addField() sets a validator');
$t->isa_ok($validatorSchema['name'], 'sfValidatorPass', '->addField() defaults to sfValidatorPass');
$t->like($form->renderHiddenFields(), '/_csrf_token/', '->renderHiddenFields() renders CSRF token after call to ->addField()');
