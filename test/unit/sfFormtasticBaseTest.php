<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(21, new lime_output_color);

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

$form->bind();
$t->ok(!$form->isValid(), '->isValid() says test form is invalid');
$t->like($form->renderHiddenFields(), '/^<input/', '->renderHiddenFields() does not render errors');

$form = new sfFormtasticBase;
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

try
{
  $form->setIdFormat('foo');
  $t->fail('->setIdFormat() throws an exception when parameter does not include "%s"');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->setIdFormat() throws an exception when parameter does not include "%s"');
}

$form->setIdFormat('my_id_format_%s');
$t->is($form->getWidgetSchema()->getOption('id_format'), 'my_id_format_%s', '->setIdFormat() sets the widgetSchema "id_format" option');
$t->is($form->getIdFormat(), 'my_id_format_%s', '->getIdFormat() returns the "id_format" option');
$t->like($form->render(), '/id="my_id_format_/', '->render() respects global id format');
$t->like($form->renderHiddenFields(), '/id="my_id_format_/', '->renderHiddenFields() respects global id format');

$form->setIdFormat(false);
$t->unlike($form->render(), '/id="/', '"->setIdFormat(false)" removes id attribute');

$t->diag('->bind()');
$form->bind(array('name' => 'xyztastic'));
$t->isa_ok($form->getErrorSchema(), 'sfValidatornatorErrorSchema', '->bind() copies native error schema');
$t->is(count($form->getErrorSchema()), 1, '->bind() copies the correct number of errors');
