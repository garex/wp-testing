<?php

class WpTesting_Doer_PassingBrowser extends WpTesting_Doer_AbstractDoer
{

    public function renderAdminPassingsPage()
    {
        $table = new WpTesting_Widget_PassingTable($this->wp);
        $this->wp->doAction('wp_testing_passing_browser_create_table', $table);
        $table->prepare_items();

        $this->output('Passing/Browser/view-all', array(
            'page'  => $this->getRequestValue('page'),
            'table' => $table,
        ));
    }

}
