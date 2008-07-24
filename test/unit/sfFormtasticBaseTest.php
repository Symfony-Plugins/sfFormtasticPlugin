<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(17, new lime_output_color);

$autoload = sfSimpleAutoload::getInstance();
$autoload->addDirectory(dirname(__FILE__).'/../../lib');
$autoload->register();

sfForm::enableCSRFProtection('secretastic');

$form = new sfFormtasticBase;

$t->isa_ok($form->getValidatorSchema(), 'sfValidatornatorSchemaBase', '->getValidatorSchema() returns overloaded class');
$t->isa_ok($form->getWidgetSchema(), 'sfWidgetasticFormSchemaBase', '->getWidgetSchema() returns overloaded class');
$t->isa_ok($form->getErrorSchema(), 'sfValidatornatorErrorSchemaBase', '->getErrorSchema() returns overloaded class');
$t->isa_ok($form->getFormFieldSchema(), 'sfFormtasticFieldSchemaBase', '->getFormFieldSchema() returns overloaded class');

$t->diag('->renderHiddenFields()');

$t->like($form->renderHiddenFields(), '/_csrf_token/', '->renderHiddenFields() renders CSRF token');
$form->setWidgets(array('email' => new sfWidgetFormInput));
$form->setValidators(array('email' => new sfValidatorEmail));
$t->like($form->renderHiddenFields(), '/_csrf_token/', '->renderHiddenFields() renders CSRF token after form has been modified');

$widgetSchema = $form->getWidgetSchema();
$validatorSchema = $form->getValidatorSchema();

$t->diag('->addField()');

$form->addField('name', new sfWidgetFormInput);
$t->ok(isset($form['name']), '->addField() sets a form field');
$t->ok(isset($widgetSchema['name']), '->addField() sets a widget');
$t->ok(isset($validatorSchema['name']), '->addField() sets a validator');
$t->isa_ok($validatorSchema['name'], 'sfValidatorPass', '->addField() defaults to sfValidatorPass');
$t->like($form->renderHiddenFields(), '/_csrf_token/', '->renderHiddenFields() renders CSRF token after call to ->addField()');

$t->diag('->setIdFormat()');

$form->setIdFormat('my_id_format_%s');
$t->is($form->getOption('id_format'), 'my_id_format_%s', '->setIdFormat() sets the "id_format" option');
$t->is($form->getIdFormat(), 'my_id_format_%s', '->getIdFormat() returns the "id_format" option');
$t->like($form->render(), '/id="my_id_format_/', '->render() respects global id format');
$t->like($form->renderHiddenFields(), '/id="my_id_format_/', '->renderHiddenFields() respects global id format');

$form->setIdFormat(false);
$t->like($form->render(), '/id="email"/', '"->setIdFormat(false)" resets id attribute on labeled fields');
$t->unlike($form->render(), '/id="\w*_csrf_token/', '"->setIdFormat(false)" removes id attribute on non-labeled fields');
