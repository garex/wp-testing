<?php

class WpTesting_Model_Environment_PhpVersion extends WpTesting_Model_Environment_Plain
{
    protected $label = 'PHP version';

    protected function calculate()
    {
        return phpversion();
    }
}
