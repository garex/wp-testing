<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddMultipleAnswersOption extends BaseMigration
{

    public function up()
    {
        $this->add_meta('wpt_test_page_multiple_answers', 0);
    }

    public function down()
    {
        $this->remove_meta('wpt_test_page_multiple_answers');
    }

}
