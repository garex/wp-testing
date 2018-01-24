<?php

class WpTesting_Doer_Feedbacker extends WpTesting_Doer_AbstractDoer
{
    /**
     * @var WpTesting_Model_Plugin
     */
    private $pluginMeta;

    public function __construct(
        WpTesting_WordPressFacade $wp,
        WpTesting_Model_Plugin $pluginMeta
    ) {
        parent::__construct($wp);
        $this->pluginMeta = $pluginMeta;

        $this->wp
            ->addFilter('plugin_row_meta', array($this, 'onPluginMeta'), WpTesting_WordPress_IPriority::PRIORITY_DEFAULT, 2)
            ->addAction('admin_menu', array($this, 'addPages'))
            ->addAction('wp_ajax_wpt_rateus', array($this, 'ajaxRateUs'))
            ->addAction('wp_testing_editor_tests_screen', array($this, 'customizeEditor'))
            ->addAction('add_meta_boxes_wpt_test', array($this, 'customizeEditorMetaboxes'))
        ;
    }

    public function customizeEditor()
    {
        $this
            ->enqueueScript('feedback', array('jquery'))
            ->addJsData('nonce',  array(
                'feedbackRateUs' => $this->pluginMeta->getRateUsNonce(),
            ))
        ;

        $this->wp->addFilter('admin_footer_text', array($this, 'renderFooter'));
    }

    public function customizeEditorMetaboxes()
    {
        $this->wp->addMetaBox('wpt_feedback', __('Feedback', 'wp-testing'),
            array($this, 'renderEditorMetabox'), 'wpt_test', 'side', 'low');
    }

    /**
     * @param array $pluginMeta
     * @param string $pluginFile
     */
    public function onPluginMeta($pluginMeta, $pluginFile)
    {
        if ($pluginFile != $this->pluginMeta->getBaseName()) {
            return $pluginMeta;
        }

        $link = '<a href="%s">%s</a>';
        return $pluginMeta + array(
            'addons'  => sprintf($link, $this->pluginMeta->getPaidAddonsUrl(),  __('Paid add-ons', 'wp-testing')),
            'support' => sprintf($link, $this->pluginMeta->getPaidSupportUrl(), __('Paid support', 'wp-testing')),
        );
    }

    public function addPages()
    {
        $hook = $this->wp->addSubmenuPage(
            'edit.php?post_type=wpt_test',
            __('Paid add-ons', 'wp-testing'),
            __('Paid add-ons', 'wp-testing'),
            'activate_plugins',
            'wpt_feedback_paid_addons',
            array($this, 'redirectToPaidAddons')
        );

        $this->wp->addAction('load-' . $hook, array($this, 'redirectToPaidAddons'));

        $hook = $this->wp->addSubmenuPage(
            null,
            null,
            null,
            'activate_plugins',
            'wpt_feedback_report_issue',
            array($this, 'renderReportIssue')
        );
        $this->wp->addAction('load-' . $hook, array($this, 'loadReportIssue'));

        $hook = $this->wp->addSubmenuPage(
            null,
            null,
            null,
            'activate_plugins',
            'wpt_feedback_get_support',
            array($this, 'renderGetSupport')
        );
        $this->wp->addAction('load-' . $hook, array($this, 'loadGetSupport'));
    }

    public function redirectToPaidAddons()
    {
        $this->wp->redirect($this->pluginMeta->getPaidAddonsUrl(), 302);
        $this->wp->dieMessage('', '', array('response' => 302));
    }

    public function loadReportIssue()
    {
        $this->wp->setAdminPageTitle(__('Report the problem', 'wp-testing'));
    }

    public function renderReportIssue()
    {
        if (!$this->isPost()) {
            $this->output('Feedback/report-issue');
        } elseif ($this->getRequestValue('environment', 'boolean')) {
            unset($_POST['environment']);
            $this->output('Feedback/environment', array(
                'wp'         => $this->wp,
                'values'     => $_POST,
                'parameters' => $this->pluginMeta->getEnvironment(),
            ));
        } else {
            $this->output('Feedback/report-issue-finish', array(
                'issueRepeats' => $this->getRequestValue('issue_repeats', 'array'),
                'expected'     => $this->getRequestValue('expected', 'string'),
                'actual'       => $this->getRequestValue('actual', 'string'),
                'screenshot'   => $this->getRequestValue('screenshot', 'string'),
                'steps'        => $this->getRequestValue('steps', 'string'),
                'parameters'   => $this->render('Feedback/environment-finish', array(
                    'parameters'   => $this->getRequestValue('parameters', 'array'),
                ))
            ));
        }
    }

    public function loadGetSupport()
    {
        $this->wp->setAdminPageTitle(__('Get the support', 'wp-testing'));

        $this
            ->registerScripts()
            ->enqueueStyle('admin')
            ->enqueueScript('get-support', array('jquery'))
        ;
    }

    public function renderGetSupport()
    {
        if (!$this->isPost()) {
            $this->output('Feedback/get-support');
        } elseif ($this->getRequestValue('environment', 'boolean')) {
            unset($_POST['environment']);
            $this->output('Feedback/environment', array(
                'wp'         => $this->wp,
                'values'     => $_POST,
                'parameters' => $this->pluginMeta->getEnvironment(),
            ));
        } else {
            $this->output('Feedback/get-support-finish', array(
                'asap'          => $this->getRequestValue('asap', 'integer'),
                'text'          => $this->render('Feedback/get-support-text', array(
                    'title'         => $this->getRequestValue('title', 'string'),
                    'details'       => $this->getRequestValue('details', 'string'),
                    'parameters'    => $this->render('Feedback/environment-finish', array(
                        'parameters'    => $this->getRequestValue('parameters', 'array'),
                    ))
                )),
            ));
        }
    }

    public function ajaxRateUs()
    {
        $this->pluginMeta->checkRateUsNonce();
        if ($this->wp->isCurrentUserCan('edit_posts')) {
            $this->pluginMeta->markRateUsAsClicked();
        }

        $this->wp->dieMessage();
    }

    public function renderEditorMetabox()
    {
        $this->output('Feedback/editor-metabox', $this->getRenderData());
    }

    private function getRenderData()
    {
        return array(
            'reportIssueUrl' => $this->wp->adminUrl('edit.php?post_type=wpt_test&page=wpt_feedback_report_issue'),
            'getSupportUrl' => $this->wp->adminUrl('edit.php?post_type=wpt_test&page=wpt_feedback_get_support'),
            'rateUsUrl' => $this->pluginMeta->getRateUsUrl(),
            'isRateUsClicked' => $this->pluginMeta->isRateUsClicked(),
        );
    }

    public function renderFooter($text)
    {
        return $this->render('Feedback/footer', $this->getRenderData());
    }
}
