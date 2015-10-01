<?php

/**
 * Facade, that knows about ORM
 */
interface WpTesting_Facade_IORM
{

    /**
     * @return void
     */
    public function setupORM();
}