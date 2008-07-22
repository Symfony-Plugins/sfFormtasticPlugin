<?php

/**
 * Config handler for sfFormtasticYaml.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  config
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfFormtasticConfigHandler extends sfYamlConfigHandler
{
  /**
   * @see sfConfigHandler
   */
  public function execute($configFiles)
  {
    $config = $this->getConfiguration($configFiles);
    $generator = new sfFormtasticGeneratorManager($config);
    
    return $generator->generate();
  }
  
  /**
   * @see sfConfigHandler
   */
  static public function getConfiguration(array $configFiles)
  {
    return self::parseYamls($configFiles);
  }
}
