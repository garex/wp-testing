<?php

abstract class WpTesting_Doer_TestPasser_Action extends WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_Model_Test
     */
    protected $test;

    abstract public function beforeRender(WpTesting_Model_Test $test);

    abstract public function renderContent($content, $template);

}