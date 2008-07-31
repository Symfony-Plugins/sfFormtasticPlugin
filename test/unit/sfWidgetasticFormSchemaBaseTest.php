<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(5, new lime_output_color);

$widgetSchema = new sfWidgetasticFormSchemaBase;

$t->ok(!$widgetSchema->has('name'), '->has() returns false if no widget is set');

$widgetSchema->add('name', new sfWidgetFormInput);
$t->isa_ok($widgetSchema['name'], 'sfWidgetFormInput', '->add() sets a widget');
$t->isa_ok($widgetSchema->get('name'), 'sfWidgetFormInput', '->get() returns a widget');
$t->ok($widgetSchema->has('name'), '->has() returns true if a widget has been set');

$widgetSchema->remove('name');
$t->ok(!isset($widgetSchema['name']), '->remove() unsets a widget');
