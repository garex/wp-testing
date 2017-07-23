<?php

interface WpTesting_Model_IEnvironment
{
    /**
     * @return string
     */
    public function label();

    /**
     * @return string
     */
    public function text();
}
