<?php

class WpTesting_Migration_AddLinkInDemoTest extends WpTesting_Migration_Base
{

    public function up()
    {
        $posts = $this->globalPrefix . 'posts';
        $this->execute("
            UPDATE $posts
            SET post_content = CONCAT(post_content, '\\n\\n<p style=\"color: gray; font-size: 70%; text-align: right;\">This test is a demonstration of <a href=\"http://apsiholog.ru/psychological-tests/\">psychological tests</a> plugin.</p>')
            WHERE post_type = 'wpt_test'
            AND post_title = 'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'
            AND post_content NOT LIKE '%apsiholog%' LIMIT 1
        ");
    }

    public function down()
    {
        $posts = $this->globalPrefix . 'posts';
        $this->execute("
            UPDATE $posts
            SET post_content = TRIM(REPLACE(post_content, '<p style=\"color: gray; font-size: 70%; text-align: right;\">This test is a demonstration of <a href=\"http://apsiholog.ru/psychological-tests/\">psychological tests</a> plugin.</p>', ''))
            WHERE post_type = 'wpt_test'
            AND post_title = 'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'
            AND post_content LIKE '%apsiholog%' LIMIT 1
        ");
    }
}
