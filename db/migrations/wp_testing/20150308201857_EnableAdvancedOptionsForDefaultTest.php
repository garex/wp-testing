<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class EnableAdvancedOptionsForDefaultTest extends BaseMigration
{

    public function up()
    {
        $this->update_meta_in_example('wpt_result_page_show_test_description', 0);
        $this->update_meta_in_example('wpt_test_page_show_progress_meter',     1);
        $this->update_meta_in_example('wpt_result_page_show_scales_diagram',   0);
    }

    public function down()
    {
        $this->update_meta_in_example('wpt_result_page_show_test_description', 1);
        $this->update_meta_in_example('wpt_test_page_show_progress_meter',     0);
        $this->update_meta_in_example('wpt_result_page_show_scales_diagram',   0);
    }

}
