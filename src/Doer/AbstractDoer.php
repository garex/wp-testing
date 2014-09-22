<?php

abstract class WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->wp = $wp;
    }

    protected function output($__template, $__params = array())
    {
        if (substr($__template, -4) != '.php') {
            $__template = dirname(dirname(__FILE__)) . '/Template/' . $__template . '.php';
        }
        extract($__params, EXTR_SKIP);
        include $__template;
    }

    protected function render($__template, $__params = array())
    {
        ob_start();
        $this->output($__template, $__params);
        return ob_get_clean();
    }
}
