<?php

class WpTesting_Model_FormulaVariable_NullValue extends WpTesting_Model_FormulaVariable
{
    public function __construct($source)
    {
        $this->setSource($source)->setValue(12)->setValueAsRatio(0.34);
    }
}