<?php

// register the sfFormtastic autoloader to enable autoloading of YAML forms
$cacheFile = sfConfig::get('sf_config_cache_dir').'/sfFormtasticAutoloadCache.txt';

$autoload = sfFormtasticAutoload::getInstance($cacheFile);
$autoload->register();
