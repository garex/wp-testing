<?php

class AddQuestionsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $table = $this->create_table(WPT_DB_PREFIX . 'questions', array(
            'id' => false,
        ));
        $table->column('question_id',    'biginteger', array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
            'auto_increment' => true,
        ));
        $table->column('test_id',        'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('question_title', 'text',       array(
            'null' => false,
        ));
        $table->finish();
        $global_prefix = WP_DB_PREFIX;
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}questions
            ADD CONSTRAINT fk_question_test
            FOREIGN KEY (test_id)
            REFERENCES {$global_prefix}posts (ID)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_question_test (test_id)
        ");
    }

    public function down()
    {
        $this->drop_table(WPT_DB_PREFIX . 'questions');
    }
}
