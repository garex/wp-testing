<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddPassingDetails extends BaseMigration
{
    public function up()
    {
        $this->add_column(WPT_DB_PREFIX . 'passings', 'ip',          'string', array('limit' => 45));
        $this->add_column(WPT_DB_PREFIX . 'passings', 'device_uuid', 'uuid');
    }

    public function down()
    {
        $this->remove_column(WPT_DB_PREFIX . 'passings', 'ip');
        $this->remove_column(WPT_DB_PREFIX . 'passings', 'device_uuid');
    }
}
