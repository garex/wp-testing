<?php

class AddPassingsTables extends Ruckusing_Migration_Base
{
    public function up()
    {
        $table = $this->create_table(WPT_DB_PREFIX . 'passings', array(
            'id' => false,
        ));
        $table->column('passing_id',    'biginteger', array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
            'auto_increment' => true,
        ));
        $table->column('test_id',        'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('respondent_id',  'biginteger', array(
            'unsigned' => true,
        ));
        $table->column('created',        'datetime',   array(
            'null' => false,
        ));
        $table->column('modified',       'datetime',   array(
            'null' => false,
        ));
        $table->finish();

        $global_prefix = WP_DB_PREFIX;
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}passings

            ADD CONSTRAINT fk_passing_test
            FOREIGN KEY (test_id)
            REFERENCES {$global_prefix}posts (ID)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_passing_test (test_id),

            ADD CONSTRAINT fk_passing_respondent
            FOREIGN KEY (respondent_id)
            REFERENCES {$global_prefix}users (ID)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_passing_respondent (respondent_id)
        ");

        $table = $this->create_table(WPT_DB_PREFIX . 'passing_answers', array(
            'id' => false,
        ));
        $pkOptions = array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
        );
        $table->column('answer_id',   'biginteger',  $pkOptions);
        $table->column('question_id', 'biginteger',  $pkOptions);
        $table->column('passing_id',  'biginteger',  $pkOptions);
        $table->finish();

        $this->execute("
            ALTER TABLE {$plugin_prefix}passing_answers

            ADD CONSTRAINT fk_passing_answer_answer
            FOREIGN KEY (answer_id)
            REFERENCES {$global_prefix}terms (term_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_answer (answer_id),

            ADD CONSTRAINT fk_passing_answer_question
            FOREIGN KEY (question_id)
            REFERENCES {$plugin_prefix}questions (question_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_question (question_id),

            ADD CONSTRAINT fk_passing_answer_passing
            FOREIGN KEY (passing_id)
            REFERENCES {$plugin_prefix}passings (passing_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_passing_answer_passing (passing_id)
        ");
    }

    public function down()
    {
        $this->drop_table(WPT_DB_PREFIX . 'passing_answers');
        $this->drop_table(WPT_DB_PREFIX . 'passings');
    }
}
