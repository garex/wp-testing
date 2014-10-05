<?php

abstract class WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

    /**
     * @var string
     */
    private $sessionPrefix = null;

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
            ->registerPluginScript('lodash', 'js/vendor/lodash/lodash.compat.min.js', array(), '2.4.1')
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

    /**
     * @param string $prefix
     * @return WpTesting_Doer_AbstractDoer
     */
    protected function sessionInit($prefix = __CLASS__)
    {
        @session_start();
        $this->sessionPrefix = $prefix;
        return $this;
    }

    /**
     * @param string $key
     * @param string|array $value
     * @return WpTesting_Doer_AbstractDoer
     */
    protected function sessionAdd($key, $value)
    {
        $_SESSION[$this->sessionPrefix . '::' . $key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return boolean
     */
    protected function sessionHas($key)
    {
        return !empty($_SESSION[$this->sessionPrefix . '::' . $key]);
    }

    /**
     * @param string $key
     * @return string|array|null Value
     */
    protected function sessionGet($key)
    {
        if (!$this->sessionHas($key)) {
            return null;
        }
        return $_SESSION[$this->sessionPrefix . '::' . $key];
    }

    /**
     * @param string $key
     * @return string|array|null Value
     */
    protected function sessionGetRemove($key)
    {
        $value = $this->sessionGet($key);
        if ($this->sessionHas($key)) {
            unset($_SESSION[$this->sessionPrefix . '::' . $key]);
        }
        return $value;
    }
}
