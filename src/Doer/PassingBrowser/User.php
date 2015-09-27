<?php

class WpTesting_Doer_PassingBrowser_User extends WpTesting_Doer_PassingBrowser
{

    protected $passingTableClass = 'WpTesting_Widget_PassingTable_User';

    protected function addMenuPages()
    {
        $mainTitle      = __('Tests', 'wp-testing');
        $resultsTitle   = __('Results', 'wp-testing');
        $capability     = 'read';
        $mainSlug       = 'wpt_test_user_results';
        $callback       = array($this, 'renderPassingsPage');
        $menuIcon       = $this->isWordPressAlready('3.8') ? 'dashicons-editor-paste-text' : null;

        $this->wp->addMenuPage($mainTitle, $mainTitle, $capability, $mainSlug, $callback, $menuIcon, 5);
        $this->setScreenHook($this->wp->addSubmenuPage($mainSlug, $resultsTitle, $resultsTitle, $capability, $mainSlug, $callback));
        $this->passingsPageTitle = __('Results', 'wp-testing');

        return $this;
    }
}
