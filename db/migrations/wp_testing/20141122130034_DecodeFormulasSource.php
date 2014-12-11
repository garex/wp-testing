<?php

class DecodeFormulasSource extends Ruckusing_Migration_Base
{
    public function up()
    {
        $rows = $this->select_all('SELECT * FROM ' . WPT_DB_PREFIX . 'formulas');
        foreach ($rows as $row) {
            $this->query(
                'UPDATE ' . WPT_DB_PREFIX . 'formulas SET formula_source = "' .
                $this->quote_string(urldecode($row['formula_source'])) .
                '" WHERE formula_id = ' . $row['formula_id']
            );
        }
    }

    public function down()
    {
        // do nothing
    }
}
