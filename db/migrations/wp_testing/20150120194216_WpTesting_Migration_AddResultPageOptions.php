<?php

class WpTesting_Migration_AddResultPageOptions extends WpTesting_Migration_AddMeta
{

    protected $metas = array(
        'wpt_result_page_show_scales' => 1,
        'wpt_result_page_show_test_description' => 1,
    );
}
