<?php

class DecimalizeScores extends Ruckusing_Migration_Base
{

    public function up()
    {
        $this->change_column(WPT_DB_PREFIX . 'scores', 'score_value', 'decimal', array(
            'precision' => 6,
            'scale'     => 3,
            'null'      => false,
            'default'   => 0,
        ));
    }

    public function down()
    {
        $this->change_column(WPT_DB_PREFIX . 'scores', 'score_value', 'tinyinteger', array(
            'null'      => false,
            'default'   => 0,
        ));
    }
}
