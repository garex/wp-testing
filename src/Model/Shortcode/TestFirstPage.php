<?php

class WpTesting_Model_Shortcode_TestFirstPage extends WpTesting_Model_Shortcode_Test
{

    const NAME = 'wpt_test_first_page';

    public function getDataForTemplate(WpTesting_Facade_IORM $ormAware)
    {
        return array_merge(parent::getDataForTemplate($ormAware), array(
            'content' => null,
        ));
    }
}
