<?php

abstract class WpTesting_Model_Environment_Plain extends WpTesting_Model_Environment_Base
{
    public function __construct()
    {
        try {
            parent::__construct($this->calculate());
        } catch (Exception $e) {
            parent::__construct($e);
        }
    }

    /**
     * @return string
     */
    abstract protected function calculate();
}
