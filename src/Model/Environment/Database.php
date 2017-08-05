<?php

abstract class WpTesting_Model_Environment_Database extends WpTesting_Model_Environment_Base
{
    public function __construct(fDatabase $db)
    {
        try {
            parent::__construct($this->calculate($db));
        } catch (Exception $e) {
            parent::__construct($e);
        }
    }

    /**
     * @return string
     */
    abstract protected function calculate(fDatabase $db);
}
