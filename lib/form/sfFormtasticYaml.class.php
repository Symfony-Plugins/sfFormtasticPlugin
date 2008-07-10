<?php

/**
 * Generate a form object from a YAML configuration file.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  form
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfFormtasticYaml extends sfFormtastic
{
  /**
   * Constructor.
   * 
   * @param string $yamlFile    YAML configuration to load
   * @param array  $defaults    An array of field default values
   * @param array  $options     An array of options
   * @param string $CRFSSecret  A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   */
  public function __construct($yamlFile, $defaults = array(), $options = array(), $CSRFSecret = null)
  {
  }
}
