<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddUserAgentToPassing extends BaseMigration
{
    public function up()
    {
        $this->add_column(WPT_DB_PREFIX . 'passings', 'user_agent', 'text');
    }

    public function down()
    {
        $this->remove_column(WPT_DB_PREFIX . 'passings', 'user_agent');
    }
}
