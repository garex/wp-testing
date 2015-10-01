<?php

class WpTesting_Migration_DecimalizeScores extends WpTesting_Migration_MigrateColumn
{

    public function up()
    {
        $this->changeColumn($this->pluginPrefix . 'scores', 'score_value', 'decimal', array(
            'precision' => 6,
            'scale'     => 3,
            'null'      => false,
            'default'   => 0,
        ));
    }

    public function down()
    {
        $this->changeColumn($this->pluginPrefix . 'scores', 'score_value', 'tinyinteger', array(
            'null'      => false,
            'default'   => 0,
        ));
    }
}
