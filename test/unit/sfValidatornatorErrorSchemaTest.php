<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(1, new lime_output_color);

class CreditCardForm extends sfFormtastic
{
  public function configure()
  {
    $this->setWidgets(array(
      'number'    => new sfWidgetFormInput,
      'exp_month' => new sfWidgetFormInput,
      'exp_year'  => new sfWidgetFormInput,
    ));

    $this->setValidators(array(
      'number'    => new sfValidatorNumber,
      'exp_month' => new sfValidatorNumber(array('min' => 1, 'max' => 12)),
      'exp_year'  => new sfValidatorNumber(array('min' => date('Y'))),
    ));

    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('exp_month', sfValidatorSchemaCompare::LESS_THAN, 'exp_year', array('throw_global_error' => true)));
  }
}

$form = new CreditCardForm;
$form->bind();

$t->diag('->toJson()');
$t->is($form->getErrorSchema()->toJson(), '{"_global":["Invalid."],"number":["Required."],"exp_month":["Required."],"exp_year":["Required."]}', '->toJson() returns the error schema serialized as JSON');
