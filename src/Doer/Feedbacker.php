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
            ->addAction('admin_menu', array($this, 'addPaidLinks'))
            ->addAction('wp_ajax_wpt_rateus', array($this, 'ajaxRateUs'))
            ->addAction('wp_testing_editor_customize_ui_after', array($this, 'customizeEditor'));
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

        $this->wp
            ->addMetaBox('wpt_feedback', __('Feedback', 'wp-testing'),
                array($this, 'renderEditorMetabox'), 'wpt_test', 'side', 'core')
            ->addFilter('admin_footer_text', array($this, 'renderFooter'))
        ;
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

    public function addPaidLinks()
    {
        $hook = $this->wp->addSubmenuPage(
            'edit.php?post_type=wpt_test',
            __('Paid add-ons', 'wp-testing'),
            __('Paid add-ons', 'wp-testing'),
            'activate_plugins',
            'wpt_test_paid_addons',
            array($this, 'redirectToPaidAddons')
        );

        $this->wp->addAction('load-' . $hook, array($this, 'redirectToPaidAddons'));
    }

    public function redirectToPaidAddons()
    {
        $this->wp->redirect($this->pluginMeta->getPaidAddonsUrl(), 302);
        $this->wp->dieMessage('', '', array('response' => 302));
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
            'reportIssueUrl' => '#report',
            'getSupportUrl' => '#support',
            'rateUsUrl' => $this->pluginMeta->getRateUsUrl(),
            'isRateUsClicked' => $this->pluginMeta->isRateUsClicked(),
        );
    }

    public function renderFooter($text)
    {
        return $this->render('Feedback/footer', $this->getRenderData());
    }
}
