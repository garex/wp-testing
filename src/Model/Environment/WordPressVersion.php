<?php

class WpTesting_Model_Environment_WordPressVersion extends WpTesting_Model_Environment_WordPress
{
    protected $label = 'WordPress version';

    protected function calculate(WpTesting_WordPressFacade $wp)
    {
        return $wp->getVersion();
    }
}
