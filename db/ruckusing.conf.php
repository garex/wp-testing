<?php
$php53 = version_compare(PHP_VERSION, '5.3.2', '>=');
if (!$php53) {
    require_once dirname(__FILE__) . '/../vendor/autoload_52.php';
}
$local = dirname(__FILE__) . '/ruckusing.conf.local.php';
if (file_exists($local)) {
    return require_once $local;
}
defined('WP_PLUGIN_URL')                || define('WP_PLUGIN_URL',                  '/wp-content/plugins');
defined('WP_DB_PREFIX')                 || define('WP_DB_PREFIX',                   'wp_');
defined('WPT_DB_PREFIX')                || define('WPT_DB_PREFIX',                  WP_DB_PREFIX . 't_');
defined('RUCKUSING_SCHEMA_TBL_NAME')    || define('RUCKUSING_SCHEMA_TBL_NAME',      WPT_DB_PREFIX . 'schema_info');
defined('RUCKUSING_TS_SCHEMA_TBL_NAME') || define('RUCKUSING_TS_SCHEMA_TBL_NAME',   WPT_DB_PREFIX . 'schema_migrations');
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
        ),
    ),
    'db_dir'          => $databaseDirectory,
    'migrations_dir'  => array('default' => $databaseDirectory . '/migrations'),
    'log_dir'         => $databaseDirectory . '/log',
);
