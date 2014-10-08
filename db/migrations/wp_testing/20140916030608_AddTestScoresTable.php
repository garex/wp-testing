<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddTestScoresTable extends BaseMigration
{
    public function up()
    {
        $table = $this->create_table(WPT_DB_PREFIX . 'scores', array(
            'id'      => false,
            'options' => 'ENGINE=' . $this->get_wp_table_engine(),
        ));
        $pkOptions = array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
        );
        $table->column('answer_id',   'biginteger',  $pkOptions);
        $table->column('question_id', 'biginteger',  $pkOptions);
        $table->column('scale_id',    'biginteger',  $pkOptions);
        $table->column('score_value', 'tinyinteger', array(
            'null'    => false,
            'default' => 0,
        ));
        $table->finish();

        $global_prefix = WP_DB_PREFIX;
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}scores

            ADD CONSTRAINT fk_score_answer
                FOREIGN KEY (answer_id)
                REFERENCES {$global_prefix}terms (term_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_score_answer (answer_id),

            ADD CONSTRAINT fk_score_question
                FOREIGN KEY (question_id)
                REFERENCES {$plugin_prefix}questions (question_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_score_question (question_id),

            ADD CONSTRAINT fk_score_scale
                FOREIGN KEY (scale_id)
                REFERENCES {$global_prefix}terms (term_id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            ADD INDEX fk_score_scale (scale_id)
        ");
    }

    public function down()
    {
        $this->drop_table(WPT_DB_PREFIX . 'scores');
    }
}
