<?php
ini_set('error_reporting', E_ALL | E_STRICT);

require_once dirname(__FILE__) . '/../../src/WordPressFacade.php';
require_once dirname(__FILE__) . '/../../src/Facade.php';
require_once dirname(__FILE__) . '/Mock/WordPressFacade.php';
require_once dirname(__FILE__) . '/Mock/Facade.php';
require_once dirname(__FILE__) . '/TestCase.php';

if (!function_exists('__')) {
    function __($key) { return $key; }
}
