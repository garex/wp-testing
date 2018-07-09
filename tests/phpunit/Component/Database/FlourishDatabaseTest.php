<?php

/**
 * @group db-connection
 */
class FlourishDatabaseTest extends DbConnectionTest
{
    protected function connectToDbAndQueryOne(WpTesting_WordPressFacade $wp)
    {
        $db = new WpTesting_Component_Database_FlourishDatabase($wp);

        return $db->query('select 1')->fetchScalar();
    }
}
