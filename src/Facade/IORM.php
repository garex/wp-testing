<?php

/**
 * Facade, that knows about ORM
 */
interface WpTesting_Facade_IORM
{

    /**
     * @return fDatabase
     */
    public function setupORM();

    /**
     * @return string
     */
    public function getTablePrefix();
}