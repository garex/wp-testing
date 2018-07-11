<?php

/**
 * Value object.
 */
abstract class WpTesting_WordPress_Value_Base
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
