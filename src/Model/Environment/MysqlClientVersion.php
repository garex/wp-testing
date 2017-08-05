<?php

class WpTesting_Model_Environment_MysqlClientVersion extends WpTesting_Model_Environment_Plain
{
    protected $label = 'MySQL client version';

    protected function calculate()
    {
        if (function_exists('mysqli_get_client_info')) {
            return mysqli_get_client_info();
        }

        if (function_exists('mysql_get_client_info')) {
            return mysql_get_client_info();
        }

        return null;
    }
}
