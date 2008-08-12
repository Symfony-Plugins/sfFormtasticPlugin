<?php

// register sfFormtasticYamlConfigHandler and sfFormtasticYamlAutoload to 
// enable autoloading of YAML forms

$this->getConfigCache()->registerConfigHandler('form/*.yml', 'sfFormtasticYamlConfigHandler');

$autoload = sfFormtasticYamlAutoload::getInstance(sfConfig::get('sf_config_cache_dir').'/sfFormtasticYamlAutoloadCache.txt');
$autoload->register();
