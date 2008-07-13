<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(6, new lime_output_color);

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
