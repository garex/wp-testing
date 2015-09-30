<?php

/**
 * Facade, that knows about TestPasser
 */
interface WpTesting_Facade_ITestPasser
{

    /**
     * @return WpTesting_Doer_TestPasser
     */
    public function getTestPasser();
}
