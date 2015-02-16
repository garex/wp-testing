<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddProgressMeterOption extends BaseMigration
{

    public function up()
    {
        $this->add_meta('wpt_test_page_show_progress_meter', 0);
    }

    public function down()
    {
        $this->remove_meta('wpt_test_page_show_progress_meter');
    }

}
