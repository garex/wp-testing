<?php

class WpTesting_Doer_Installer extends WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_Facade_IORM
     */
    private $ormAware;

    /**
     * Decide in which mode to work: for all sites (when in multisite mode) or only for current site
     *
     * @var boolean
     */
    private $isNetworkWide;

    public function __construct(WpTesting_WordPressFacade $wp, WpTesting_Facade_IORM $ormAware)
    {
        parent::__construct($wp);
        $this->ormAware = $ormAware;

        $this->wp
            ->registerActivationHook(array($this,              'onActivate'))
            ->registerDeactivationHook(array($this,            'onDeactivate'))
            ->registerUninstallHook(array(get_class($this),    'onUninstallStatic'))
            ->addFilter('upgrader_post_install', array($this,  'onUpgrade'), WpTesting_WordPress_IPriority::PRIORITY_DEFAULT, 2)
            ->addAction('wpmu_new_blog', array($this,          'onBlogCreate'))
            ->addAction('delete_blog', array($this,            'onBlogRemove'), WpTesting_WordPress_IPriority::PRIORITY_DEFAULT, 2)
        ;
    }

    /**
     * @param boolean $isNetworkWide
     */
    public function onActivate($isNetworkWide = false)
    {
        __('Helps to create psychological tests.', 'wp-testing');

        $this->setNetworkWide($isNetworkWide)->runOnBlogs('upgrade');
    }

    /**
     * @param boolean $isNetworkWide
     */
    public function onDeactivate($isNetworkWide = false)
    {
        $this->setNetworkWide($isNetworkWide)->runOnBlogs('deactivate');
    }

    public function onUninstall()
    {
        $this->setNetworkWide($this->wp->isMultisite())->runOnBlogs('uninstall');
    }

    public static function onUninstallStatic()
    {
        $wp = new WpTesting_WordPressFacade('../../wp-testing.php');
        $me = new WpTesting_Doer_Installer($wp, new WpTesting_Facade($wp));
        $me->onUninstall();
    }

    /**
     * @param boolean $return
     * @param array $extra
     * @return boolean
     */
    public function onUpgrade($return, $extra)
    {
        $isCurrentPluginUpgrade = (isset($extra['plugin']) && $extra['plugin'] == $this->wp->getPluginBaseName());
        if ($isCurrentPluginUpgrade) {
            $this->setNetworkWide()->runOnBlogs('upgrade');
        }
        return $return;
    }

    /**
     * @param integer $blogId
     */
    public function onBlogCreate($blogId)
    {
        $this->ormAware->setupORM();
        $this->wp->switchToBlog($blogId);
        $this->setNetworkWide(true)->upgradeOnBlog();
        $this->wp->restoreCurrentBlog();
    }

    /**
     * @param integer $blogId
     * @param boolean $isDropTables
     */
    public function onBlogRemove($blogId, $isDropTables = false)
    {
        $this->ormAware->setupORM();
        $this->setNetworkWide(true)->uninstallOnBlog($isDropTables);
    }

    /**
     * @param boolean|null $value
     * @return WpTesting_Doer_Installer
     */
    private function setNetworkWide($value = null)
    {
        if (!$this->wp->isMultisite()) {
            $value = false;
        } elseif (is_null($value)) {
            $value = $this->wp->isPluginActiveForNetwork();
        }
        $this->isNetworkWide = $value;
        return $this;
    }

    private function upgradeOnBlog()
    {
        $this->migrateDatabase(array(__FILE__, 'db:migrate'));
        new WpTesting_Doer_WordPressEntitiesRegistrator($this->wp);
        $this->flushRewrite();
    }

    private function deactivateOnBlog()
    {
        $this->flushRewrite();
    }

    private function uninstallOnBlog($isDropTables = true)
    {
        if ($isDropTables) {
            $adapter = $this->migrateDatabase(array(__FILE__,'db:migrate','VERSION=0'));
            $adapter->drop_table($adapter->get_schema_version_table_name());
            if ($adapter instanceof Ruckusing_Adapter_Base) {
                $adapter->logger->close();
            }
        }
        $this->flushRewrite();
    }

    private function runOnBlogs($method)
    {
        $function = array($this, $method . 'OnBlog');
        if (!$this->isNetworkWide) {
            call_user_func($function);
            return;
        }

        $this->ormAware->setupORM();
        $blogs = WpTesting_Query_Blog::create()->findAll();
        foreach ($blogs->getPrimaryKeys() as $blogId) {
            $this->wp->switchToBlog($blogId);
            call_user_func($function);
            $this->wp->restoreCurrentBlog();
        }
    }

    /**
     * @param array $argv
     * @return Ruckusing_Adapter_Base
     */
    private function migrateDatabase($argv)
    {
        $wp0Prefix = $this->wp->getGlobalTablePrefix();
        $wpPrefix  = $this->wp->getTablePrefix();
        $wptPrefix = $this->ormAware->getTablePrefix();

        $runnerReflection = new ReflectionClass('Ruckusing_FrameworkRunner');
        defined('RUCKUSING_WORKING_BASE')       || define('RUCKUSING_WORKING_BASE',         dirname(dirname(dirname(__FILE__))));
        defined('RUCKUSING_BASE')               || define('RUCKUSING_BASE',                 dirname(dirname(dirname($runnerReflection->getFileName()))));

        $databaseDirectory = RUCKUSING_WORKING_BASE . '/db';
        $dbHostWithPort    = explode(':', $this->wp->getDbHost() . ':3306');
        $config = array(
            'db' => array(
                'development' => array(
                    'type'     => 'mysql',
                    'host'     => reset($dbHostWithPort),
                    'port'     => next($dbHostWithPort),
                    'database' => $this->wp->getDbName(),
                    'directory'=> 'wp_testing',
                    'user'     => $this->wp->getDbUser(),
                    'password' => $this->wp->getDbPassword(),
                    'charset'  => $this->wp->getDbCharset(),
                    'globalPrefix' => $wp0Prefix,
                    'blogPrefix'   => $wpPrefix,
                    'pluginPrefix' => $wptPrefix,
                    'schema_version_table_name' => $wptPrefix . 'schema_migrations',
                ),
            ),
            'db_dir'         => $databaseDirectory,
            'migrations_dir' => array('default' => $databaseDirectory . '/migrations'),
            'log_dir'        => $this->wp->getTempDir() . 'wp_testing_' . md5(__FILE__),
        );

        $runner = new Ruckusing_FrameworkRunner($config, $argv);
        restore_error_handler();
        restore_exception_handler();
        $runner->execute();

        /* @var $adapter Ruckusing_Adapter_Base */
        $adapter = $runner->get_adapter();
        if ($adapter instanceof Ruckusing_Adapter_Base) {
            $adapter->logger = new Ruckusing_Util_Logger($config['log_dir'] . '/development.log');
        }
        return $adapter;
    }

    /**
     * Flush rewrite rules
     *
     * @return void
     */
    private function flushRewrite()
    {
        $this->wp->getRewrite()->flush_rules();
    }
}
