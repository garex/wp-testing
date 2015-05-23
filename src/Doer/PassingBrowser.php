<?php

class WpTesting_Doer_PassingBrowser extends WpTesting_Doer_AbstractDoer
{

    public function registerUserPages()
    {
        $mainTitle      = __('Tests', 'wp-testing');
        $resultsTitle   = __('Results', 'wp-testing');
        $capability     = 'read';
        $mainSlug       = 'wpt_test_user_results';
        $callback       = array($this, 'renderUserPassingsPage');
        $menuIcon       = $this->isWordPressAlready('3.8') ? 'dashicons-editor-paste-text' : null;

        $this->wp
        ->addMenuPage(              $mainTitle,    $mainTitle,    $capability, $mainSlug, $callback, $menuIcon, 5)
        ->addSubmenuPage($mainSlug, $resultsTitle, $resultsTitle, $capability, $mainSlug, $callback)
        ;
    }

    public function renderAdminPassingsPage()
    {
        $this->renderPassingTable(new WpTesting_Widget_PassingTable_Admin($this->wp));
    }

    public function renderUserPassingsPage()
    {
        $this->renderPassingTable(new WpTesting_Widget_PassingTable_User($this->wp));
    }

    private function renderPassingTable(WpTesting_Widget_PassingTable $table)
    {
        $this->wp->doAction('wp_testing_passing_browser_create_table', $table);
        $table->prepare_items();

        $this->output('Passing/Browser/view-all', array(
                'page'  => $this->getRequestValue('page'),
                'table' => $table,
        ));
    }
}
