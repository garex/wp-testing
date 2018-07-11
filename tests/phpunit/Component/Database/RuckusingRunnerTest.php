<?php

/**
 * @group db-connection
 */
class RuckusingRunnerTest extends DbConnectionTest
{
    protected function connectToDbAndQueryOne(WpTesting_WordPressFacade $wp)
    {
        $runner = new WpTesting_Component_Database_RuckusingRunner($wp, self::getFacade(), array());

        $result = $runner->get_adapter()->query('select 1');

        return $result[0][1];
    }
}