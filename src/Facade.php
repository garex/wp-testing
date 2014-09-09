<?php

class WpTesting_Facade
{

    /**
     * @var WpTesting_ShortcodeProcessor
     */
    private $shortcodeProcessor = null;

    public static function onPluginActivate()
    {
        $me = new self();
        $me->migrateDatabase(array(__FILE__, 'db:migrate'));
    }

    public static function onPluginDeactivate()
    {
        $me = new self();
   }

    public static function onPluginUninstall()
    {
        $me = new self();
        $adapter = $me->migrateDatabase(array(__FILE__, 'db:migrate', 'VERSION=0'));
        $adapter->drop_table(RUCKUSING_TS_SCHEMA_TBL_NAME);
        $adapter->logger->close();
    }

    public function shortcodeList()
    {
        return $this->getShortcodeProcessor()->getList();
    }

    protected function getShortcodeProcessor()
    {
        if (!is_null($this->shortcodeProcessor)) {
            return $this->shortcodeProcessor;
        }

        $this->setupORM();
        require_once dirname(__FILE__) . '/ShortcodeProcessor.php';
        $this->shortcodeProcessor = new WpTesting_ShortcodeProcessor();

        return $this->shortcodeProcessor;
    }

    protected function setupORM()
    {
        $this->autoloadComposer();

        // Extract port from host. See wpdb::db_connect
        $port = null;
        $host = DB_HOST;
        if (preg_match('/^(.+):(\d+)$/', trim($host), $m)) {
            $host = $m[1];
            $port = $m[2];
        }
        $database = new fDatabase('mysql', DB_NAME, DB_USER, DB_PASSWORD, $host, $port);
        fORMDatabase::attach($database);

        require_once dirname(__FILE__) . '/Model/AbstractModel.php';
        require_once dirname(__FILE__) . '/Model/Test.php';
        require_once dirname(__FILE__) . '/Query/AbstractQuery.php';
        require_once dirname(__FILE__) . '/Query/Test.php';

        fORM::mapClassToTable('WpTesting_Model_Test', 'wp_t_tests');
    }

    /**
     * @param array $argv
     * @return Ruckusing_Adapter_Interface
     */
    protected function migrateDatabase($argv)
    {
        $this->autoloadComposer();

        $runnerReflection = new ReflectionClass('Ruckusing_FrameworkRunner');
        defined('WPT_DB_PREFIX')                or define('WPT_DB_PREFIX',                  $GLOBALS['table_prefix'] . 't_');
        defined('DB_TYPE')                      or define('DB_TYPE',                        'mysql');
        defined('RUCKUSING_SCHEMA_TBL_NAME')    or define('RUCKUSING_SCHEMA_TBL_NAME',      WPT_DB_PREFIX . 'schema_info');
        defined('RUCKUSING_TS_SCHEMA_TBL_NAME') or define('RUCKUSING_TS_SCHEMA_TBL_NAME',   WPT_DB_PREFIX . 'schema_migrations');
        defined('RUCKUSING_WORKING_BASE')       or define('RUCKUSING_WORKING_BASE',         dirname(dirname(__FILE__)));
        defined('RUCKUSING_BASE')               or define('RUCKUSING_BASE',                 dirname(dirname(dirname($runnerReflection->getFileName()))));

        $databaseDirectory = RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'db';
        $config = array(
            'db' => array(
                'development' => array(
                    'type'     => DB_TYPE,
                    'host'     => reset(explode(':', DB_HOST)),
                    'port'     => next(explode(':', DB_HOST . ':3306')),
                    'database' => DB_NAME,
                    'user'     => DB_USER,
                    'password' => DB_PASSWORD,
                    'charset'  => DB_CHARSET,
                ),
            ),
            'db_dir'         => $databaseDirectory,
            'migrations_dir' => array('default' => $databaseDirectory . DIRECTORY_SEPARATOR . 'migrations'),
            'log_dir'        => $databaseDirectory . DIRECTORY_SEPARATOR . 'log',
        );

        $runner = new Ruckusing_FrameworkRunner($config, $argv);
        restore_error_handler();
        restore_exception_handler();
        $runner->execute();

        /* @var $adapter Ruckusing_Adapter_Interface */
        $adapter = $runner->get_adapter();
        $adapter->logger = new Ruckusing_Util_Logger($config['log_dir'] . DIRECTORY_SEPARATOR . 'development.log');
        return $adapter;
    }

    protected function autoloadComposer()
    {
        // 1. Try to find composer.json
        $composerFullName = null;
        foreach (array(ABSPATH, dirname(dirname(WP_PLUGIN_DIR))) as $path) {
            $candidateFile = $path . DIRECTORY_SEPARATOR . 'composer.json';
            if (file_exists($candidateFile)) {
                $composerFullName = $candidateFile;
                break;
            }
        }

        // 2. Not found? Use default php52 generated autoloader
        if (!$composerFullName) {
            $autoloadPath = implode(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), 'vendor', 'autoload_52.php'));
            require_once ($autoloadPath);
            return;
        }

        // 3. Found? Determine vendor dirname and load autoload file
        $vendorDirectory = 'vendor';
        if (function_exists('json_decode')) {
            $composerJson = json_decode(file_get_contents($composerFullName), true);
            if (!empty($composerJson['config']['vendor-dir'])) {
                $vendorDirectory = $composerJson['config']['vendor-dir'];
            }
        }

        $autoloadPath = implode(DIRECTORY_SEPARATOR, array(dirname($composerFullName), $vendorDirectory, 'autoload.php'));
        require_once ($autoloadPath);
    }

}
