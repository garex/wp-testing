<?php

class WpTesting_Doer_Shortcoder_TestReadMore extends WpTesting_Doer_Shortcoder
{

    protected function createShortcode(array $attributes)
    {
        return new WpTesting_Model_Shortcode_TestReadMore($this->wp, $attributes);
    }

    protected function chooseTemplate(WpTesting_Model_Shortcode $shortcode)
    {
        return 'Shortcode/test_read_more';
    }
}
