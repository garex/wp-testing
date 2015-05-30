<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddStatusToPassings extends BaseMigration
{
    public function up()
    {
        $options = array(
            'after'   => 'respondent_id',
            'values'  => array('publish', 'trash'),
            'default' => 'publish',
            'null'    => false,
        );
        $this->add_column(WPT_DB_PREFIX . 'passings', 'passing_status', 'enum', $options);

        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}passings
            ADD INDEX i_passing_status_created_id (passing_status, passing_created, passing_id)
        ");
    }

    public function down()
    {
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}passings
            DROP INDEX i_passing_status_created_id
        ");
        $this->remove_column(WPT_DB_PREFIX . 'passings', 'passing_status');
    }
}
