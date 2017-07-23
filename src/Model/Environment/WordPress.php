<?php

abstract class WpTesting_Model_Environment_WordPress extends WpTesting_Model_Environment_Base
{
    public function __construct(WpTesting_WordPressFacade $wp)
    {
        try {
            parent::__construct($this->calculate($wp));
        } catch (Exception $e) {
            parent::__construct($e);
        }
    }

    /**
     * @return string
     */
    abstract protected function calculate(WpTesting_WordPressFacade $wp);
}
