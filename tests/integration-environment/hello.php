<?php
/**
 * Plugin Name: Hello Dolly With Upgrade
 * Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
 * Description: Symbolizes the hope and enthusiasm of an entire generation.
 * Version: 0.1
 * Author: Matt Mullenweg
 * Author URI: http://ma.tt/
 */

add_filter('upgrader_post_install', 'hello_dolly_upgrade', 10, 2);
function hello_dolly_upgrade($return, $extra)
{
    $isCurrentPluginUpgrade = (isset($extra['plugin']) && $extra['plugin'] == plugin_basename(__FILE__));
    if (!$isCurrentPluginUpgrade || !is_admin()) {
        return $return;
    }

    global $wpdb;

    $wpdb->update(
        $wpdb->options,
        array('option_value' => 'Upgraded from plugin'),
        array('option_name'  => 'blogname')
    );

    return $return;
}
