<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddTestPageOptions extends BaseMigration
{

    public function up()
    {
        $this->add_meta('wpt_test_page_submit_button_caption',   '');
        $this->add_meta('wpt_test_page_reset_answers_on_back',   0);
    }

    public function down()
    {
        $this->remove_meta('wpt_test_page_submit_button_caption');
        $this->remove_meta('wpt_test_page_reset_answers_on_back');
    }

}
