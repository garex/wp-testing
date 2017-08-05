<?php

class WpTesting_Model_Environment_ActiveTheme extends WpTesting_Model_Environment_WordPress
{
    protected $label = 'Active theme stylesheet';

    protected function calculate(WpTesting_WordPressFacade $wp)
    {
        return $wp->getOption('stylesheet');
    }
}
