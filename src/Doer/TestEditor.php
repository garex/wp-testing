<?php

class WpTesting_Doer_TestEditor extends WpTesting_Doer_AbstractDoer
{

    /**
     * @param WP_Screen $screen
     */
    public function customizeUi($screen)
    {
        if (!$this->isTestScreen($screen)) {
            return;
        }
        $this->sessionInit(__CLASS__)->wp
            ->enqueuePluginStyle('wpt_admin', 'css/admin.css')
            ->enqueuePluginScript('wpt_test_edit_fix_styles',  'js/test-edit-fix-styles.js',        array('jquery'), false, true)
            ->enqueuePluginScript('field_selection',           'js/vendor/kof/field-selection.js',  array('jquery'), false, true)
            ->enqueuePluginScript('wpt_test_edit_formulas',    'js/test-edit-formulas.js',          array('jquery'), false, true)
            ->addAction('post_submitbox_misc_actions', array($this, 'renderSubmitMiscActions'))
            ->addAction('media_buttons',               array($this, 'renderContentEditorButtons'))
            ->addMetaBox('wpt_edit_questions', 'Edit Questions',    array($this, 'renderEditQuestions'), 'wpt_test')
            ->addMetaBox('wpt_add_questions',  'Add New Questions', array($this, 'renderAddQuestions'),  'wpt_test')
            ->addMetaBox('wpt_edit_formulas',  'Edit Formulas',     array($this, 'renderEditFormulas'),  'wpt_test')
            ->addAction('admin_notices', array($this, 'printAdminMessages'))
            ->addAction('save_post',     array($this, 'saveTest'), 10, 2)
        ;
    }

    public function renderSubmitMiscActions()
    {
        // Set metadata defaults
        $isPublishOnHome = $this->wp->getCurrentPostMeta('wpt_publish_on_home');
        if ($isPublishOnHome == '') {
            $isPublishOnHome = '1';
        }
        $this->output('Test/Editor/submit-misc-actions', array(
            'isPublishOnHome' => $isPublishOnHome,
        ));
    }

    public function renderContentEditorButtons($editorId)
    {
        if ('content' != $editorId) {
            return;
        }
        $this->output('Test/Editor/content-editor-buttons');
    }

    /**
     * @param WP_Post $item
     */
    public function renderEditQuestions($item)
    {
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/edit-questions', array(
            'scales'      => $test->buildScalesWithRange(),
            'questions'   => $test->buildQuestions(),
            'isWarnOfSettings'   => $test->isWarnOfSettings(),
            'memoryWarnSettings' => $test->getMemoryWarnSettings(),
            'isUnderApache'      => $this->isUnderApache(),
        ));
    }

    /**
     * @param WP_Post $item
     */
    public function renderAddQuestions($item)
    {
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/add-questions', array(
            'addNewCount' => WpTesting_Model_Question::ADD_NEW_COUNT,
            'startFrom'   => $test->buildQuestions()->count(),
            'scales'      => $test->buildScales(),
        ));
    }

    /**
     * @param WP_Post $item
     */
    public function renderEditFormulas($item)
    {
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/edit-formulas', array(
            'results'    => $test->buildResults(),
            'variables'  => $test->buildFormulaVariables(),
        ));
    }

    /**
     * @param integer $id
     * @param WP_Post $item
     */
    public function saveTest($id, $item)
    {
        $test = new WpTesting_Model_Test($item);
        if (!$test->getId()) {
            return;
        }

        // Update metadata only when we have appropriate keys
        $isPublishOnHome = $this->getRequestValue('wpt_publish_on_home');
        $isFullEdit      = (!is_null($isPublishOnHome));
        if (!$isFullEdit) {
            return;
        }

        $this->wp->updatePostMeta($test->getId(), 'wpt_publish_on_home', intval($isPublishOnHome));

        $_POST = $test->adaptForPopulate($_POST);
        $test->populateQuestions(true);
        $test->populateFormulas();

        try {
            $problems = $test->validate();
            $test->store(true);
        } catch (fValidationException $e) {
            $title = 'Test data not saved';
            $this->wp->dieMessage(
                $this->render('Test/Editor/admin-message', array(
                    'title'   => $title,
                    'content' => $e->getMessage(),
                )),
                $title,
                array('back_link' => true)
            );
        }
    }

    public function printAdminMessages()
    {
        if ($this->sessionHas('admin_message')) {
            $this->output('Test/Editor/admin-message', $this->sessionGetRemove('admin_message'));
        }
    }

    /**
     * Do we currently at tests?
     *
     * @param WP_Screen $screen
     * @return boolean
     */
    private function isTestScreen($screen)
    {
        if (!empty($screen->post_type) && $screen->post_type == 'wpt_test') {
            return true;
        }
        if ($this->isWordPressAlready('3.3')) {
            return false;
        }

        // WP 3.2 workaround
        if ($this->isPost() && $this->getRequestValue('post_type') == 'wpt_test') {
            return true;
        }

        $id = $this->getRequestValue('post');
        if (!$id) {
            return false;
        }
        $test   = new WpTesting_Model_Test($id);
        $isTest = ($test->getId()) ? true : false;
        $test->reset();
        return $isTest;
    }

    private function isUnderApache()
    {
        if (empty($_SERVER['SERVER_SOFTWARE'])) {
            return false;
        }
        return preg_match('/apache|httpd/i', $_SERVER['SERVER_SOFTWARE']);
    }
}
