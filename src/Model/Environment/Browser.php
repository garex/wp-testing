<?php

class WpTesting_Model_Environment_Browser extends WpTesting_Model_Environment_Plain
{
    protected $label = 'Browser';

    protected function calculate()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}
