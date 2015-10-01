<?php

class AllowEmptyFormulas extends BaseMigration
{
    public function up()
    {
        $this->change_column(WPT_DB_PREFIX . 'formulas', 'formula_source', 'text',   array(
            'null' => true,
        ));
    }

    public function down()
    {
        $this->change_column(WPT_DB_PREFIX . 'formulas', 'formula_source', 'text',   array(
            'null' => false,
        ));
    }
}
