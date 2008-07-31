<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(5, new lime_output_color);

$validatorSchema = new sfValidatornatorSchemaBase;

$t->ok(!$validatorSchema->has('name'), '->has() returns false if no validator is set');

$validatorSchema->add('name', new sfValidatorString);
$t->isa_ok($validatorSchema['name'], 'sfValidatorString', '->add() sets a validator');
$t->isa_ok($validatorSchema->get('name'), 'sfValidatorString', '->get() returns a validator');
$t->ok($validatorSchema->has('name'), '->has() returns true if a validator has been set');

$validatorSchema->remove('name');
$t->ok(!isset($validatorSchema['name']), '->remove() unsets a validator');
