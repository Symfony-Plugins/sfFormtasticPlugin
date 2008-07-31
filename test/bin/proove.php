<?php

require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';
$configuration = new ProjectConfiguration(realpath(dirname(__FILE__).'/../../../..'));
include $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

$autoload = sfSimpleAutoload::getInstance();
$autoload->addDirectory(dirname(__FILE__).'/../../lib');
$autoload->register();

$h = new lime_harness(new lime_output_color);
$h->register(sfFinder::type('file')->name('*Test.php')->in(dirname(__FILE__).'/..'));

exit($h->run() ? 0 : 1);
