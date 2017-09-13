<?php

class WpTesting_Migration_FixExampleTestAnswers extends WpTesting_Migration_UpdateData
{
    public function up()
    {
        $posts    = $this->blogPrefix . 'posts';
        $testSlug = 'eysencks-personality-inventory-epi-extroversionintroversion';
        $testId   = $this->field("SELECT ID FROM $posts WHERE post_type = 'wpt_test' AND post_name = '$testSlug' ORDER BY ID LIMIT 1");
        if (empty($testId)) {
            return;
        }

        $questions = $this->pluginPrefix . 'questions';
        $answers = $this->pluginPrefix . 'answers';

        $this->executeSafely("
            UPDATE $answers
            SET answer_title = NULL
            WHERE answer_title = '' AND question_id IN (
                SELECT question_id FROM $questions WHERE test_id = $testId
            )
        ");
    }

    public function down()
    {
        // do nothing
    }
}
