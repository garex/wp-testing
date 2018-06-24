<?php
$php53 = version_compare(PHP_VERSION, '5.3.2', '>=');
if (!$php53) {
    require_once dirname(__FILE__) . '/../vendor/autoload_52.php';
}
$local = dirname(__FILE__) . '/ruckusing.conf.local.php';
if (file_exists($local)) {
    return require $local;
}
$wp0Prefix = 'wp_';
$wpPrefix  = 'wp_';
$wptPrefix = 'wp_t_';
defined('WP_PLUGIN_URL')                || define('WP_PLUGIN_URL',                  '/wp-content/plugins');
defined('RUCKUSING_WORKING_BASE')       || define('RUCKUSING_WORKING_BASE',         dirname(__FILE__));
$databaseDirectory = RUCKUSING_WORKING_BASE;
return array(
    'db' => array(
        'development' => array(
            'type'     => 'mysql',
            'host'     => 'localhost',
            'port'     => 3306,
            'database' => 'wordpress',
            'directory'=> 'wp_testing',
            'user'     => 'root',
            'password' => '',
            'charset'  => 'utf8',
            'globalPrefix' => $wp0Prefix,
            'blogPrefix'   => $wpPrefix,
            'pluginPrefix' => $wptPrefix,
            'schema_version_table_name' => $wptPrefix . 'schema_migrations',
        ),
    ),
    'db_dir'          => $databaseDirectory,
    'migrations_dir'  => array('default' => $databaseDirectory . '/migrations'),
    'log_dir'         => $databaseDirectory . '/log',
);
