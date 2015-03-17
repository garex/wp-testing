<?php

class WpTesting_Mock_WordPressFacade extends WpTesting_WordPressFacade
{

    private $db = array();

    public function __construct($pluginFile, $db = array())
    {
        parent::__construct($pluginFile);
        $this->db = $db;
    }

    public function getDbHost()
    {
        return $this->db['host'];
    }

    public function getDbName()
    {
        return $this->db['database'];
    }

    public function getDbUser()
    {
        return $this->db['user'];
    }

    public function getDbPassword()
    {
        return $this->db['password'];
    }

    public function getTablePrefix()
    {
        return 'wp_';
    }

    public function getDbCharset()
    {
        return 'utf-8';
    }

    /**
     * Absolute path to the WordPress directory.
     */
    public function getAbsPath()
    {
        return dirname(__FILE__);
    }

    /**
     * Allows for the plugins directory to be moved from the default location.
     *
     * @since 2.6.0
     */
    public function getPluginDir()
    {
        return realpath(dirname(__FILE__) . '/../../..');
    }

    public function registerActivationHook($function)
    {
        return $this;
    }

    public function registerDeactivationHook($function)
    {
        return $this;
    }

    public function registerUninstallHook($function)
    {
        return $this;
    }

    public function addAction($tag, $function, $priority = 10, $functionArgsCount = 1)
    {
        return $this;
    }

    public function addFilter($tag, $function, $priority = 10, $functionArgsCount = 1)
    {
        return $this;
    }

    public function addShortcode($tag, $function)
    {
        return $this;
    }

    public function isAdministrationPage()
    {
        return false;
    }

    public function getPluginUrl($pluginRelatedPath = '')
    {
        return $pluginRelatedPath;
    }

    public function registerPluginScript($name, $pluginRelatedPath, array $dependencies = array(), $version = false, $isInFooter = false)
    {
        return $this;
    }

    public function getPermalink($id = 0, $isLeaveName = false)
    {
        if (is_object($id)) {
            $id = $id->ID;
        }
        return 'http://wpti.dev/?p='.$id;
    }

    public function getPostMeta($postId, $key, $isSingle)
    {
        return null;
    }
}
