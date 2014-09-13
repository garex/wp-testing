<?php

/**
 * Facade into wordpress
 */
class WpTesting_WordPressFacade
{

    /**
     * Plugin filename (required for hooks)
     * @var string
     */
    private $pluginFile = null;

    /**
     * @param string $pluginFile Plugin filename (required for hooks)
     */
    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    public function getDbHost()
    {
        return DB_HOST;
    }

    public function getDbName()
    {
        return DB_NAME;
    }

    public function getDbUser()
    {
        return DB_USER;
    }

    public function getDbPassword()
    {
        return DB_PASSWORD;
    }

    public function getTablePrefix()
    {
        return $GLOBALS['table_prefix'];
    }

    public function getDbCharset()
    {
        return DB_CHARSET;
    }

    /**
     * Absolute path to the WordPress directory.
     */
    public function getAbsPath()
    {
        return ABSPATH;
    }

    /**
     * Allows for the plugins directory to be moved from the default location.
     *
     * @since 2.6.0
     */
    public function getPluginDir()
    {
        return WP_PLUGIN_DIR;
    }

    /**
     * Holds the WordPress Rewrite object for creating pretty URLs
     *
     * @since 1.5.0
     * @return WP_Rewrite
     */
    public function getRewrite()
    {
        return $GLOBALS['wp_rewrite'];
    }

    /**
     * Hooks a function on to a specific action.
     *
     * @since 1.2.0
     * @link http://codex.wordpress.org/Function_Reference/add_action
     *
     * @param string $tag The name of the action to which the $function is hooked.
     * @param callback $function The name of the function you wish to be called.
     * @param int $priority optional. Used to specify the order in which the functions associated with a particular action are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
     * @param int $functionArgsCount optional. The number of arguments the function accept (default 1).
     * @return WpTesting_WordPressFacade
     */
    public function addAction($tag, callable $function, $priority = 10, $functionArgsCount = 1)
    {
        add_action($tag, $function, $priority, $functionArgsCount);
        return $this;
    }

    /**
     * Add hook for shortcode tag.
     *
     * @since 2.5.0
     * @link http://codex.wordpress.org/Function_Reference/add_shortcode
     *
     * @param string $tag Shortcode tag to be searched in post content.
     * @param callable $function Hook to run when shortcode is found.
     * @return WpTesting_WordPressFacade
     */
    public function addShortcode($tag, callable $function)
    {
        add_shortcode($tag, $function);
        return $this;
    }

    /**
     * Register a post type. Do not use before init.
     *
     * @since 2.9.0
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     *
     * @param string $name Post type key, must not exceed 20 characters.
     * @param array $parameters
     * @return WpTesting_WordPressFacade
     */
    public function registerPostType($name, $parameters = array())
    {
        register_post_type($name, $parameters);
        return $this;
    }

    /**
     * Create or modify a taxonomy object. Do not use before init.
     *
     * @since 2.3.0
     * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
     *
     * @param string $name Taxonomy key, must not exceed 32 characters.
     * @param array|string $objectType Name of the object type(s) for the taxonomy object.
     * @param array|string $parameters
     * @return WpTesting_WordPressFacade
     */
    public function registerTaxonomy($name, $objectType, $parameters = array())
    {
        register_taxonomy($name, $objectType, $parameters);
        return $this;
    }

    /**
     * Set the activation hook for a plugin.
     *
     * @since 2.0.0
     * @link http://codex.wordpress.org/Function_Reference/register_activation_hook
     *
     * @param callback $function the function hooked for action.
     * @return WpTesting_WordPressFacade
     */
    public function registerActivationHook(callable $function)
    {
        register_activation_hook($this->pluginFile, $function);
        return $this;
    }

    /**
     * Set the deactivation hook for a plugin.
     *
     * @since 2.0.0
     * @link http://codex.wordpress.org/Function_Reference/register_deactivation_hook
     *
     * @param callback $function the function hooked for action.
     * @return WpTesting_WordPressFacade
     */
    public function registerDeactivationHook(callable $function)
    {
        register_deactivation_hook($this->pluginFile, $function);
        return $this;
    }

    /**
     * Set the uninstallation hook for a plugin.
     *
     * @since 2.7.0
     * @link http://codex.wordpress.org/Function_Reference/register_uninstall_hook
     *
     * @param callback $function the function hooked for action.
     * @return WpTesting_WordPressFacade
     */
    public function registerUninstallHook(callable $function)
    {
        register_uninstall_hook($this->pluginFile, $function);
        return $this;
    }
}