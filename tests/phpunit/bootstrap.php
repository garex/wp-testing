<?php
$pluginFile = realpath(dirname(__FILE__) . '/../../wp-testing.php');
require_once dirname($pluginFile) . '/src/WordPressFacade.php';
require_once dirname($pluginFile) . '/src/Facade.php';
require_once dirname(__FILE__) . '/Mock/WordPressFacade.php';
require_once dirname(__FILE__) . '/Mock/Facade.php';

$migration = require_once dirname(__FILE__) . '/../../db/ruckusing.conf.php';
new WpTesting_Mock_Facade(
    new WpTesting_Mock_WordPressFacade(
        $pluginFile,
        $migration['db']['development']
    )
);
