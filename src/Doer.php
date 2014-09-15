<?php

abstract class WpTesting_Doer
{

    protected function output($__template, $__params = array())
    {
        $__template .= '.php';
        extract($__params, EXTR_SKIP);
        include dirname(__FILE__) . '/Template/' . $__template;
    }

    protected function render($__template, $__params = array())
    {
        ob_start();
        $this->output($__template, $__params);
        return ob_get_clean();
    }
}
