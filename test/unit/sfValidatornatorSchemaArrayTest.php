<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(5, new lime_output_color);

$validator = new sfValidatornatorSchemaArray(new sfValidatorInteger);

try
{
  $validator->clean(array());
  $t->pass('->clean() accepts an empty array');
}
catch (sfValidatorError $e)
{
  $t->fail('->clean() accepts an empty array');
}

try
{
  $validator->clean(array(1, 'foo'));
  $t->fail('->clean() rejects an array with an invalid value');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() rejects an array with an invalid value');
}

try
{
  $validator->clean(array(1, 2, 3));
  $t->pass('->clean() accepts an array of valid values');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() accepts an array of valid values');
}

$schema = new sfValidatorSchema(array('ids' => $validator));
try
{
  $schema->clean(array('ids' => array(1, 2, 3)));
  $t->pass('->clean() accepts an array of valid values when called from "sfValidatorSchema"');
}
catch (sfValidatorError $e)
{
  $t->fail('->clean() accepts an array of valid values when called from "sfValidatorSchema"');
}

try
{
  $schema->clean(array('ids' => array(1, 'foo')));
  $t->fail('->clean() rejects an array with invalid values when called from "sfValidatorSchema"');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() rejects an array with invalid values when called from "sfValidatorSchema"');
}

