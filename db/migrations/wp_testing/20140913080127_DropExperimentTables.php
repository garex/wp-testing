<?php

class DropExperimentTables extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->drop_table(WPT_DB_PREFIX . 'answers_parameters');
        $this->drop_table(WPT_DB_PREFIX . 'answers');
        $this->drop_table(WPT_DB_PREFIX . 'questions');
        $this->drop_table(WPT_DB_PREFIX . 'scales_tests');
        $this->drop_table(WPT_DB_PREFIX . 'tests');
        $this->drop_table(WPT_DB_PREFIX . 'parameters');
        $this->drop_table(WPT_DB_PREFIX . 'scales');
    }

    public function down()
    {
        // nothing here. it's backward incompatible action
    }
}
