<?php

interface WpTesting_Model_FormulaVariable_IAllBuilder
{

    /**
     * From passed test and passing builds many variables
     *
     * @param WpTesting_Model_Test $test
     * @param WpTesting_Model_Passing $passing
     * @return WpTesting_Model_FormulaVariable[]
     */
    public static function buildAllFrom(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing = null);
}