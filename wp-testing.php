<?php
/**
 * Plugin Name: Wp-testing
 * Plugin URI: http://wordpress.org/extend/plugins/wp-testing/
 * Description: Helps to create psychological tests.
 * Version: 0.0
 * Author: Alexander Ustimenko
 * Author URI: http://ustimen.co
 * License: GPL3
 */

require_once dirname(__FILE__) . '/src/Facade.php';

register_activation_hook   (__FILE__, array('WpTesting_Facade', 'onPluginActivate'));
register_deactivation_hook (__FILE__, array('WpTesting_Facade', 'onPluginDeactivate'));
register_uninstall_hook    (__FILE__, array('WpTesting_Facade', 'onPluginUninstall'));
