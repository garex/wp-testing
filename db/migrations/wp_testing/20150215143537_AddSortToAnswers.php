<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddSortToAnswers extends BaseMigration
{
    public function up()
    {
        $this->add_column(WPT_DB_PREFIX . 'answers', 'answer_sort', 'integer', array('default' => 100));
    }

    public function down()
    {
        $this->remove_column(WPT_DB_PREFIX . 'answers', 'answer_sort');
    }
}
