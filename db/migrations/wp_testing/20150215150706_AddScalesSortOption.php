<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddScalesSortOption extends BaseMigration
{

    public function up()
    {
        $this->add_meta('wpt_result_page_sort_scales_by_score', 0);
    }

    public function down()
    {
        $this->remove_meta('wpt_result_page_sort_scales_by_score');
    }

}
