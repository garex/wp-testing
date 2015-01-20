<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddResultPageOptions extends BaseMigration
{

    public function up()
    {
        $this->add_meta('wpt_result_page_show_scales',             1);
        $this->add_meta('wpt_result_page_show_test_description',   1);
    }

    public function down()
    {
        $this->remove_meta('wpt_result_page_show_scales');
        $this->remove_meta('wpt_result_page_show_test_description');
    }

}
