<?php

class WpTesting_Migration_FixExampleTest extends WpTesting_Migration_UpdateData
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
        foreach (array(
            array('otherpeople', 'other people'),
            array('feelingyou', 'feeling you'),
            array('upin', 'up in'),
            array('knew you', 'knew you'),
            array('toa', 'to a'),
            array('hear?', 'heart?'),
        ) as $row) {
            list($find, $replace) = $row;
            $this->executeSafely("
                UPDATE $questions SET question_title = REPLACE(question_title, '$find', '$replace')
                WHERE test_id = $testId AND question_title LIKE '%$find%'
            ");
        }
    }

    public function down()
    {
        // do nothing
    }
}
