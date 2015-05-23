<?php

class WpTesting_Doer_PassingBrowser_Admin extends WpTesting_Doer_PassingBrowser
{

    protected $passingTableClass = 'WpTesting_Widget_PassingTable_Admin';

    protected function addMenuPages()
    {
        $this->screenHook = $this->wp->addSubmenuPage(
            'edit.php?post_type=wpt_test',
            __('Respondents’ test results', 'wp-testing'),
            __('Respondents’ results', 'wp-testing'),
            'activate_plugins',
            'wpt_test_respondents_results',
            array($this, 'renderPassingsPage')
        );

        return $this;
    }
}
