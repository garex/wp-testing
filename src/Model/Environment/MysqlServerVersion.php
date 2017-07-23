<?php

class WpTesting_Model_Environment_MysqlServerVersion extends WpTesting_Model_Environment_Database
{
    protected $label = 'MySQL server version';

    protected function calculate(fDatabase $db)
    {
        $result = $db->query('SELECT VERSION()');

        return $result->fetchScalar();
    }
}
