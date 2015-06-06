<?php

class WpTesting_Doer_PassingBrowser_Admin extends WpTesting_Doer_PassingBrowser
{

    protected $passingTableClass = 'WpTesting_Widget_PassingTable_Admin';

    protected function processAction($action, $ids)
    {
        if (!in_array($action, array('trash', 'untrash', 'delete'))) {
            return parent::processAction($action, $ids);
        }

        $passings = WpTesting_Query_Passing::create()->findAllByIds($ids);
        if (count($passings) == 0) {
            return parent::processAction($action, $ids);
        }

        switch ($action) {
            case 'trash':
                foreach ($passings as $passing) {
                    $passing->trash();
                }
                break;

            case 'untrash':
                foreach ($passings as $passing) {
                    $passing->publish();
                }
                break;

            case 'delete':
                foreach ($passings as $passing) {
                    $passing->delete(true);
                }
                break;
        }

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
