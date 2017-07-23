<?php

class WpTesting_Model_Environment_MysqlEngines extends WpTesting_Model_Environment_Database
{
    protected $label = 'MySQL engines';

    protected function calculate(fDatabase $db)
    {
        $rows = $db->query('SHOW ENGINES')->fetchAllRows();

        $grouped = array();
        foreach ($rows as $row) {
            $grouped[$row['Support']][] = $row['Engine'];
        }

        $rows = array();
        foreach ($grouped as $support => $engines) {
            $rows[] = array('Support' => $support, 'Engines' => new WpTesting_Component_Formatter_LineList($engines));
        }

        return new WpTesting_Component_Formatter_ItemList($rows, '%Support$s: %Engines$s');
    }
}
