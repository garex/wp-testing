<?php

class WpTesting_Component_Loader
{

    /**
     * Prefix => Dirname
     * @var array
     */
    private $prefixToPath = array();

    private $requiredPrefix = '';

    public function __construct($requiredPrefix)
    {
        $this->requiredPrefix = $requiredPrefix;
        spl_autoload_register(array($this, 'autoload'));
    }

    public function autoload($class)
    {
        if (empty($this->prefixToPath) || false === strpos($class, $this->requiredPrefix)) {
            return;
        }
        $prefix = $this->getPrefix($class);
        if (!isset($this->prefixToPath[$prefix])) {
            return;
        }
        $path = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        $path = str_replace($prefix, $this->prefixToPath[$prefix], $path);
        require_once $path;
    }

    /**
     * @param WpTesting_Component_IRootable $rootable
     * @return WpTesting_Component_Loader
     */
    public function addPrefixPath($rootable)
    {
        $this->prefixToPath[$this->getPrefix($rootable->getClass())] = $rootable->getRoot();
        return $this;
    }

    private function getPrefix($class)
    {
        return substr($class, 0, strpos($class, '_'));
    }

}