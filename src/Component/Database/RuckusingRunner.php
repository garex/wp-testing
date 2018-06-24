<?php

/**
 * @method Ruckusing_Adapter_MySQL_Base get_adapter
 */
class WpTesting_Component_Database_RuckusingRunner extends Ruckusing_FrameworkRunner
{
    public function __construct(WpTesting_WordPressFacade $wp, WpTesting_Facade_IORM $ormAware, array $argv)
    {
        $wp0Prefix = $wp->getGlobalTablePrefix();
        $wpPrefix  = $wp->getTablePrefix();
        $wptPrefix = $ormAware->getTablePrefix();

        $runnerReflection = new ReflectionClass('Ruckusing_FrameworkRunner');
        defined('RUCKUSING_WORKING_BASE') || define('RUCKUSING_WORKING_BASE', dirname(dirname(dirname(dirname(__FILE__)))));
        defined('RUCKUSING_BASE')         || define('RUCKUSING_BASE',         dirname(dirname(dirname($runnerReflection->getFileName()))));

        $databaseDirectory = RUCKUSING_WORKING_BASE . '/db';
        $host = new WpTesting_WordPress_Value_DbHost($wp->getDbHost());
        $config = array(
            'db' => array(
                'development' => array(
                    'type'     => 'mysql',
                    'host'     => $host->socket() ? null : $host->host(),
                    'port'     => $host->socket() ? null : $host->port(),
                    'socket'   => $host->socket() ? $host->socket() : null,
                    'database' => $wp->getDbName(),
                    'directory'=> 'wp_testing',
                    'user'     => $wp->getDbUser(),
                    'password' => $wp->getDbPassword(),
                    'charset'  => $wp->getDbCharset(),
                    'globalPrefix' => $wp0Prefix,
                    'blogPrefix'   => $wpPrefix,
                    'pluginPrefix' => $wptPrefix,
                    'schema_version_table_name' => $wptPrefix . 'schema_migrations',
                ),
                'fixed-charset' => array(
                    'charset'  => 'utf8',
                ),
            ),
            'db_dir'         => $databaseDirectory,
            'migrations_dir' => array('default' => $databaseDirectory . '/migrations'),
            'log_dir'        => $wp->getTempDir() . 'wp_testing_' . md5(__FILE__),
        );
        $config['db']['fixed-charset'] = array_merge($config['db']['development'], $config['db']['fixed-charset']);

        parent::__construct($config, $argv);

        restore_error_handler();
        restore_exception_handler();

        // Unset logger to avoid closing it in execute and to not create outside as it was before
        unset($this->logger);
    }
}
