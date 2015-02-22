<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddScalesDiagramOption extends BaseMigration
{

    public function up()
    {
        $this->add_meta('wpt_result_page_show_scales_diagram', 0);
    }

    public function down()
    {
        $this->remove_meta('wpt_result_page_show_scales_diagram');
    }

}
