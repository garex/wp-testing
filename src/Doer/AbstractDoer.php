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
        $__template .= '.php';
        extract($__params, EXTR_SKIP);
        include dirname(dirname(__FILE__)) . '/Template/' . $__template;
    }

    protected function render($__template, $__params = array())
    {
        ob_start();
        $this->output($__template, $__params);
        return ob_get_clean();
    }
}
