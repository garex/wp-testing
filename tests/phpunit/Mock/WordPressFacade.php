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

    public function doAction($tag, $arg = '')
    {
        return true;
    }

    public function addFilter($tag, $function, $priority = 10, $functionArgsCount = 1)
    {
        return $this;
    }

    public function applyFilters($tag, $value)
    {
        return $value;
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
        return 'http://wpti.dev:8000/?p='.$id;
    }

    public function getPostMeta($postId, $key, $isSingle)
    {
        return null;
    }

    public function sanitazeShortcodeAttributes($pairs, $atts, $tag = '')
    {
        // version from 2.9
        $atts = (array)$atts;
        $out = array();
        foreach($pairs as $name => $default) {
            if ( array_key_exists($name, $atts) )
                $out[$name] = $atts[$name];
            else
                $out[$name] = $default;
        }

        // .. without filter here from 3.6

        return $out;
    }

    public function getExtended($post)
    {
        // Version from 4.3

        // Match the new style more links.
        if (preg_match('/<!--more(.*?)?-->/', $post, $matches)) {
            list($main, $extended) = explode($matches[0], $post, 2);
            $more_text = $matches[1];
        } else {
            $main = $post;
            $extended = '';
            $more_text = '';
        }

        // leading and trailing whitespace.
        $main = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $main);
        $extended = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $extended);
        $more_text = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $more_text);

        return array('main' => $main, 'extended' => $extended, 'more_text' => $more_text);
    }
}
