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
        $this->registerScripts();
    }

    /**
     * Register common used scripts for future dependencies
     */
    protected function registerScripts()
    {
        $this->wp
            ->registerPluginScript('lodash-source', 'js/vendor/lodash/lodash.compat.min.js', array(), '2.4.1')
            ->registerPluginScript('lodash', 'js/vendor/lodash/lodash.no-conflict.js', array('lodash-source'))
        ;
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

    protected function isPost()
    {
        return fRequest::isPost();
    }

    protected function getRequestValue($key)
    {
        return fRequest::get($key);
    }

    /**
     * Checks if current WordPress version greater than or equal provided version
     *
     * @param string $version
     * @return boolean
     */
    protected function isWordPressAlready($version)
    {
        return version_compare($this->wp->getVersion(), $version, '>=');
    }

}
