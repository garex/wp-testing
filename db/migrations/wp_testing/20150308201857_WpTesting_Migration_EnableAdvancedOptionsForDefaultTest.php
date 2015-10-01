<?php

class WpTesting_Migration_EnableAdvancedOptionsForDefaultTest extends WpTesting_Migration_Base
{

    public function up()
    {
        $this->updateMetaInExample('wpt_result_page_show_test_description', 0);
        $this->updateMetaInExample('wpt_test_page_show_progress_meter',     1);
        $this->updateMetaInExample('wpt_result_page_show_scales_diagram',   0);
    }

    public function down()
    {
        $this->updateMetaInExample('wpt_result_page_show_test_description', 1);
        $this->updateMetaInExample('wpt_test_page_show_progress_meter',     0);
        $this->updateMetaInExample('wpt_result_page_show_scales_diagram',   0);
    }

    protected function updateMetaInExample($key, $value)
    {
        $this->execute("
            UPDATE {$this->globalPrefix}posts AS p, {$this->globalPrefix}postmeta AS m
            SET m.meta_value = '$value'
            WHERE TRUE
            AND m.meta_key   = '$key'
            AND p.post_type  = 'wpt_test'
            AND p.post_title = 'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'
            AND m.post_id    = p.ID
        ");
    }
}
