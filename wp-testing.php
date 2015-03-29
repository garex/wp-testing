<?php
/**
 * Plugin Name: Wp-testing
 * Plugin URI: http://wordpress.org/extend/plugins/wp-testing/
 * Description: Helps to create psychological tests.
 * Version: 0.10
 * Author: Alexander Ustimenko
 * Author URI: http://ustimen.co
 * License: GPL3
 * Text Domain: wp-testing
 * Domain Path: /languages
 */

require_once dirname(__FILE__) . '/src/WordPressFacade.php';
require_once dirname(__FILE__) . '/src/Facade.php';

$WpTesting_Facade = new WpTesting_Facade(new WpTesting_WordPressFacade(__FILE__));
