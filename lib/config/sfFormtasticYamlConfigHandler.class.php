<?php

/**
 * Config handler for YAML form classes.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  config
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfFormtasticYamlConfigHandler extends sfYamlConfigHandler
{
  /**
   * @see sfConfigHandler
   */
  public function execute($configFiles)
  {
    $generatorManager = new sfGeneratorManager(sfProjectConfiguration::getActive());
    return $generatorManager->generate('sfFormtasticYamlGenerator', $this->getConfiguration($configFiles));
  }
  
  /**
   * @see sfConfigHandler
   */
  static public function getConfiguration(array $configFiles)
  {
    return self::parseYamls($configFiles);
  }
}
