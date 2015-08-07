<?php

class AllowSectionQuestionsDuplicates extends Ruckusing_Migration_Base
{
    public function up()
    {
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}sections
            DROP INDEX uq_section_test_question
        ");
    }

    public function down()
    {
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}sections
            ADD UNIQUE INDEX uq_section_test_question (test_id, question_id)
        ");
    }
}
