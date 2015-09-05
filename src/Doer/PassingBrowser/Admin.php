<?php

class WpTesting_Doer_PassingBrowser_Admin extends WpTesting_Doer_PassingBrowser
{

    protected $passingTableClass = 'WpTesting_Widget_PassingTable_Admin';

    public function registerPages()
    {
        parent::registerPages();

        $this->wp
            ->addAction('wp_testing_passing_browser_admin_process_trash',   array($this, 'processActionTrash'),   WpTesting_Addon_IWordPressFacade::PRIORITY_DEFAULT, 2)
            ->addAction('wp_testing_passing_browser_admin_process_untrash', array($this, 'processActionUntrash'), WpTesting_Addon_IWordPressFacade::PRIORITY_DEFAULT, 2)
            ->addAction('wp_testing_passing_browser_admin_process_delete',  array($this, 'processActionDelete'),  WpTesting_Addon_IWordPressFacade::PRIORITY_DEFAULT, 2)
        ;

        return $this;
    }

    /**
     * @param WpTesting_Model_Passing[] $passings
     * @param self $me
     * @return self
     */
    public function processActionTrash(fRecordSet $passings, WpTesting_Doer_PassingBrowser_Admin $me)
    {
        foreach ($passings as $passing) { /* @var $passing WpTesting_Model_Passing */
            $passing->trash();
        }
        return $this;
    }

    /**
     * @param WpTesting_Model_Passing[] $passings
     * @param self $me
     * @return self
     */
    public function processActionUntrash(fRecordSet $passings, WpTesting_Doer_PassingBrowser_Admin $me)
    {
        foreach ($passings as $passing) { /* @var $passing WpTesting_Model_Passing */
            $passing->publish();
        }
        return $this;
    }

    /**
     * @param WpTesting_Model_Passing[] $passings
     * @param self $me
     * @return self
     */
    public function processActionDelete(fRecordSet $passings, WpTesting_Doer_PassingBrowser_Admin $me)
    {
        foreach ($passings as $passing) { /* @var $passing WpTesting_Model_Passing */
            $passing->delete(true);
        }
        return $this;
    }

    protected function processAction($action, $ids)
    {
        $passings = WpTesting_Query_Passing::create()->findAllByIdsSorted($ids);

        $this->wp->doAction('wp_testing_passing_browser_admin_process_' . $action, $passings, $this);

        $referer = $this->wp->getReferer();
        if ($referer) {
            return $this->wp->safeRedirect($referer);
        }

        return $this->wp->redirect('?post_type=wpt_test&page=wpt_test_respondents_results');
    }

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

        $this->passingsPageTitle = __('Respondents’ test results', 'wp-testing');

        return $this;
    }
}
