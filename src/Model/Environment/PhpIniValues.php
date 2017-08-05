<?php

class WpTesting_Model_Environment_PhpIniValues extends WpTesting_Model_Environment_Plain
{
    protected $label = 'PHP settings';

    private $keys = array(
        'auto_append_file',
        'auto_prepend_file',
        'disable_classes',
        'disable_functions',
        'display_errors',
        'error_log',
        'error_reporting',
        'log_errors',
        'max_execution_time',
        'max_input_time',
        'max_input_vars',
        'memory_limit',
    );

    protected function calculate()
    {
        $rows = array();

        foreach ($this->keys as $key) {
            $value = trim(ini_get($key));
            if (empty($value)) {
                continue;
            }
            if (preg_match('/^disable_/', $key)) {
                $value = explode(',', trim($value, ','));
                $value = new WpTesting_Component_Formatter_LineList($value);
            }
            $rows[] = array('Name' => $key, 'Value' => $value);
        }

        return new WpTesting_Component_Formatter_ItemList($rows, '%Name$s: %Value$s');
    }
}
