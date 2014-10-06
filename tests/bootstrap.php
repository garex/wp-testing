<?php
require_once dirname(__FILE__) . '/../vendor/autoload_52.php';
$migration = require_once dirname(__FILE__) . '/../db/ruckusing.conf.php';
$db        = $migration['db']['development'];
$database  = new fDatabase('mysql', $db['database'], $db['user'], $db['password'], $db['host'], $db['port']);
// $database->enableDebugging(true);
fORMDatabase::attach($database);
fORM::mapClassToTable('WpTesting_Model_Formula', WPT_DB_PREFIX  . 'formulas');
fORM::mapClassToTable('WpTesting_Model_Scale',   WP_DB_PREFIX   . 'terms');
