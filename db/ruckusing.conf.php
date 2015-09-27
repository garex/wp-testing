<?php
require_once dirname(__FILE__) . '/../vendor/ruckusing/ruckusing-migrations/lib/Ruckusing/Migration/Base.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR .  'wp_testing' . DIRECTORY_SEPARATOR . '_BaseMigration.php';

$local = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ruckusing.conf.local.php';
if (file_exists($local)) {
    return require_once $local;
}
defined('WP_PLUGIN_URL')                or define('WP_PLUGIN_URL',                  '/wp-content/plugins');
defined('WP_DB_PREFIX')                 or define('WP_DB_PREFIX',                   'wp_');
defined('WPT_DB_PREFIX')                or define('WPT_DB_PREFIX',                  WP_DB_PREFIX . 't_');
defined('RUCKUSING_SCHEMA_TBL_NAME')    or define('RUCKUSING_SCHEMA_TBL_NAME',      WPT_DB_PREFIX . 'schema_info');
defined('RUCKUSING_TS_SCHEMA_TBL_NAME') or define('RUCKUSING_TS_SCHEMA_TBL_NAME',   WPT_DB_PREFIX . 'schema_migrations');
defined('RUCKUSING_WORKING_BASE')       or define('RUCKUSING_WORKING_BASE',         dirname(__FILE__));
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
    'migrations_dir'  => array('default' => $databaseDirectory . DIRECTORY_SEPARATOR . 'migrations'),
    'log_dir'         => $databaseDirectory . DIRECTORY_SEPARATOR . 'log',
);
