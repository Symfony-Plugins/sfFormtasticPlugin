<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test;
$t->output = new lime_output_color;

// include every file in the lib
foreach(sfFinder::type('file')->name('*.php')->in(dirname(__FILE__).'/../../lib') as $file)
{
  include_once $file;
  $t->pass(basename($file).' parsed ok');
}
