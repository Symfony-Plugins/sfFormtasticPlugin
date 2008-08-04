<?php

// register sfFormtasticYamlAutoload to enable autoloading of YAML forms
$cacheFile = sfConfig::get('sf_config_cache_dir').'/sfFormtasticYamlAutoloadCache.txt';

$autoload = sfFormtasticYamlAutoload::getInstance($cacheFile);
$autoload->register();
