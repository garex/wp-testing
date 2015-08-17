<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddSectionsTable extends BaseMigration
{

    public function up()
    {
        $this->drop_table(WPT_DB_PREFIX . 'sections');
        $table = $this->create_table(WPT_DB_PREFIX . 'sections', array(
            'id'      => false,
            'options' => $this->get_table_engine_option(),
        ));
        $table->column('section_id',    'biginteger', array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
            'auto_increment' => true,
        ));
        $table->column('test_id',   'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('question_id',   'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('section_title', 'text',       array(
            'null' => false,
        ));
        $table->finish();

        $global_prefix = WP_DB_PREFIX;
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}sections

            ADD CONSTRAINT {$plugin_prefix}fk_section_test
            FOREIGN KEY (test_id)
            REFERENCES {$global_prefix}posts (ID)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_section_test (test_id),

            ADD CONSTRAINT {$plugin_prefix}fk_section_question
            FOREIGN KEY (question_id)
            REFERENCES {$plugin_prefix}questions (question_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_section_question (question_id),

            ADD UNIQUE INDEX uq_section_test_question (test_id, question_id)
        ");
    }

    public function down()
    {
        $this->drop_table(WPT_DB_PREFIX . 'sections');
    }
}
