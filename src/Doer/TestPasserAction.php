<?php

abstract class WpTesting_Doer_TestPasserAction extends WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_Model_Test
     */
    protected $test;

    /**
     * @var WpTesting_Model_Passing
     */
    protected $passing;

    /**
     * @param WpTesting_Model_Test $test
     * @param WpTesting_Model_Passing $passing
     * @return void
     */
    abstract public function beforeRender(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing = null);

    /**
     * @param string $content
     * @param string $template
     * @return string
     */
    abstract public function renderContent($content, $template);
}
