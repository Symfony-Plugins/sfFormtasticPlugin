<?php

/**
 * Autoloader for YAML form classes.
 * 
 * @package     sfFormtasticPlugin
 * @subpackage  autoload
 * @author      Kris Wallsmith
 * @version     SVN: $Id$
 */
class sfFormtasticAutoload
{
  static protected
    $instance = null;
  
  protected
    $cacheFile    = null,
    $cacheLoaded  = false,
    $cacheChanged = false,
    $classes      = null;
  
  /**
   * Constructor.
   */
  protected function __construct($cacheFile = null)
  {
    if (!is_null($cacheFile))
    {
      $this->cacheFile = $cacheFile;
    }
    
    $this->loadCache();
  }
  
  /**
   * Retrieves the singleton instance of this class.
   *
   * @param  string $cacheFile  The file path to save the cache
   *
   * @return sfFormtasticAutoload   A sfFormtasticAutoload implementation instance.
   */
  static public function getInstance($cacheFile = null)
  {
    if (!isset(self::$instance))
    {
      self::$instance = new sfFormtasticAutoload($cacheFile);
    }
    
    return self::$instance;
  }
  
  /**
   * Register sfFormtasticAutoload in spl autoloader.
   *
   * @return void
   */
  static public function register()
  {
    ini_set('unserialize_callback_func', 'spl_autoload_call');
    if (!spl_autoload_register(array(self::getInstance(), 'autoload')))
    {
      throw new sfException(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));
    }
    
    if (self::getInstance()->cacheFile)
    {
      register_shutdown_function(array(self::getInstance(), 'saveCache'));
    }
  }
  
  /**
   * Unregister sfFormtasticAutoload from spl autoloader.
   *
   * @return void
   */
  static public function unregister()
  {
    spl_autoload_unregister(array(self::getInstance(), 'autoload'));
  }
  
  /**
   * Handles autoloading of classes.
   *
   * @param  string  A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function autoload($class)
  {
    // class already exists
    if (class_exists($class, false) || interface_exists($class, false))
    {
      return true;
    }
    
    // load class array if necessary
    if (is_null($this->classes))
    {
      $this->reload();
    }
    
    // we have a YAML for this class, include it
    if (isset($this->classes[$class]))
    {
      require sfContext::getInstance()->getConfigCache()->checkConfig($this->classes[$class]);
      
      return true;
    }
    
    return false;
  }
  
  /**
   * Loads the cache.
   */
  public function loadCache()
  {
    if (!$this->cacheFile || !is_readable($this->cacheFile))
    {
      return;
    }
    
    $this->classes = unserialize(file_get_contents($this->cacheFile));
    
    $this->cacheLoaded = true;
    $this->cacheChanged = false;
  }
  
  /**
   * Saves the cache.
   */
  public function saveCache()
  {
    if ($this->cacheChanged)
    {
      file_put_contents($this->cacheFile, serialize($this->classes));
      
      $this->cacheChanged = false;
    }
  }
  
  /**
   * Reloads cache.
   * 
   * @todo A more elegant usage of sfFinder...
   */
  public function reload()
  {
    $this->classes = array();
    $this->cacheLoaded = false;
    
    $in = array(
      sfConfig::get('sf_config_dir'),
      sfConfig::get('sf_app_config_dir'),
    );
    foreach (ProjectConfiguration::getActive()->getPluginPaths() as $pluginDir)
    {
      if (is_dir($pluginDir.'/config'))
      {
        $in[] = $pluginDir.'/config';
      }
    }
    
    // find all config/form directories
    $dirs = sfFinder::type('dir')->name('form')->in($in);
    
    // find all *.yml form files
    $files = sfFinder::type('file')->name('*.yml')->in($dirs);
    
    foreach ($files as $file)
    {
      // mine class names
      if (preg_match_all('/^(\w+):/m', file_get_contents($file), $matches))
      {
        foreach ($matches[1] as $class)
        {
          $this->classes[$class] = str_replace(sfConfig::get('sf_root_dir').'/', '', $file);
        }
      }
    }
    
    $this->cacheLoaded = true;
    $this->cacheChanged = true;
  }
  
  /**
   * Removes the cache.
   */
  public function removeCache()
  {
    @unlink($this->cacheFile);
  }
}
