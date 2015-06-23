<?php

class NullifySectionQuestion extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->change_column(WPT_DB_PREFIX . 'sections', 'question_id',   'biginteger', array(
            'unsigned' => true,
            'null'     => true,
        ));
    }

    public function down()
    {
        $this->change_column(WPT_DB_PREFIX . 'sections', 'question_id',   'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
    }
}
