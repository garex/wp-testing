<?php

class WpTesting_Migration_DecodeFormulasSource extends WpTesting_Migration_UpdateData
{

    public function up()
    {
        $rows = $this->selectAll('SELECT * FROM ' . $this->pluginPrefix . 'formulas');
        foreach ($rows as $row) {
            $this->execute(
                'UPDATE ' . $this->pluginPrefix . 'formulas SET formula_source = "' .
                $this->quoteString(urldecode($row['formula_source'])) .
                '" WHERE formula_id = ' . $row['formula_id']
            );
        }
    }

    public function down()
    {
        // do nothing
    }
}
