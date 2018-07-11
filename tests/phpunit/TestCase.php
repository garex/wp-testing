<?php

abstract class WpTesting_Tests_TestCase extends PHPUnit_Framework_TestCase
{
    private static $wpFacade;

    private static $facade;

    public static function setUpBeforeClass()
    {
        self::$wpFacade = self::createWordPressFacade();
        self::$facade = self::createFacade(self::$wpFacade);
    }

    /**
     * @return WpTesting_WordPressFacade
     */
    protected function getWpFacade()
    {
        return self::$wpFacade;
    }

    /**
     * @return WpTesting_Facade
     */
    protected function getFacade()
    {
        return self::$facade;
    }

    protected static function createWordPressFacade(array $dbOverride = array())
    {
        $pluginFile = realpath(dirname(__FILE__) . '/../../wp-testing.php');
        $migration = require dirname(__FILE__) . '/../../db/ruckusing.conf.php';

        return new WpTesting_Mock_WordPressFacade($pluginFile, $dbOverride + $migration['db']['development']);
    }

    private static function createFacade(WpTesting_WordPressFacade $wp)
    {
        fORMDatabase::reset();

        return new WpTesting_Mock_Facade($wp);
    }
}