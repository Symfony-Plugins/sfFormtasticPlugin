<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(0, new lime_output_color);

class myForm extends sfFormtastic
{
  public function configure()
  {
    $this->setWidgets(array(
      'date'          => new sfWidgetasticFormDate,
      'datetime'      => new sfWidgetasticFormDateTime,
      'input'         => new sfWidgetasticFormInput,
      'checkbox'      => new sfWidgetasticFormInputCheckbox,
      'file'          => new sfWidgetasticFormInputFile,
      'hidden'        => new sfWidgetasticFormInputHidden,
      'password'      => new sfWidgetasticFormInputPassword,
      'select'        => new sfWidgetasticFormSelect(array('choices' => array())),
      'select_many'   => new sfWidgetasticFormSelectMany(array('choices' => array())),
      'select_radio'  => new sfWidgetasticFormSelectRadio(array('choices' => array())),
      'textarea'      => new sfWidgetasticFormTextarea,
      'time'          => new sfWidgetasticFormTime,
    ));
    
    foreach ($this->widgetSchema as $field => $widget)
    {
      $this->validatorSchema[$field] = new sfValidatorPass;
    }
  }
}

$form = new myForm;
