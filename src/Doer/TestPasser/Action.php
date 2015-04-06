<?php

abstract class WpTesting_Doer_TestPasser_Action extends WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_Model_Test
     */
    protected $test;

    /**
     * @var WpTesting_Model_Passing
     */
    protected $passing;

    abstract public function beforeRender(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing = null);

    abstract public function renderContent($content, $template);

}