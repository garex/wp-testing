<?php

class WpTesting_Migration_AddTestPageOptions extends WpTesting_Migration_AddMeta
{

    protected $metas = array(
        'wpt_test_page_submit_button_caption' => '',
        'wpt_test_page_reset_answers_on_back' => 0,
    );
}
