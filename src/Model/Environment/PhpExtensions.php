<?php

class WpTesting_Model_Environment_PhpExtensions extends WpTesting_Model_Environment_Plain
{
    protected $label = 'PHP extensions';

    protected function calculate()
    {
        $rows = get_loaded_extensions();

        natcasesort($rows);

        return new WpTesting_Component_Formatter_LineList($rows);
    }
}
