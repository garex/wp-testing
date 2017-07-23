<?php

class WpTesting_Model_Environment_MysqlTableStatus extends WpTesting_Model_Environment_Database
{
    protected $label = 'MySQL table status';

    protected function calculate(fDatabase $db)
    {
        $rows = $db->query('SHOW TABLE STATUS')->fetchAllRows();

        $grouped = array();
        foreach ($rows as $row) {
            $key = (string)new WpTesting_Component_Formatter_NamedArrayKeys($row, '%Engine$s@%Version$s, %Collation$s, %Row_format$s');
            $value = new WpTesting_Component_Formatter_NamedArrayKeys($row, '%Name$s: %Rows$d rows');
            $grouped[$key][] = (string)$value;
        }

        return new WpTesting_Component_Formatter_ListFromArray($grouped);
    }
}
