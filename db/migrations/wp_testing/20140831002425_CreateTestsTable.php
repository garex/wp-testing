<?php

class CreateTestsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table(WPT_DB_PREFIX . 'tests');
        $t->column('title', 'string');
        $t->finish();
    }

    public function down()
    {
        $this->drop_table(WPT_DB_PREFIX . 'tests');
    }
}
