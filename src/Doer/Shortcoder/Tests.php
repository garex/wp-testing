<?php

class WpTesting_Doer_Shortcoder_Tests extends WpTesting_Doer_Shortcoder
{

    protected function createShortcode(array $attributes)
    {
        return new WpTesting_Model_Shortcode_Tests($this->wp, $attributes);
    }

    protected function chooseTemplate(WpTesting_Model_Shortcode $shortcode)
    {
        return 'Shortcode/tests';
    }
}
