<?php

interface WpTesting_Component_IRootable
{

    /**
     * @return string Class name placed in root
     */
    public function getClass();

    /**
     * @return string Absolute path without ending slash
     */
    public function getRoot();

}