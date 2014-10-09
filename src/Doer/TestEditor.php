<?php

class WpTesting_Doer_TestEditor extends WpTesting_Doer_AbstractDoer
{

    public function customizeUi()
    {
        $this->sessionInit(__CLASS__)->wp
            ->addAction('media_buttons', array($this, 'renderContentEditorButtons'))
            ->addMetaBox('wpt_edit_questions', 'Edit Questions',    array($this, 'renderEditQuestions'), 'wpt_test')
            ->addMetaBox('wpt_add_questions',  'Add New Questions', array($this, 'renderAddQuestions'),  'wpt_test')
            ->addMetaBox('wpt_edit_formulas',  'Edit Formulas',     array($this, 'renderEditFormulas'),  'wpt_test')
            ->addAction('admin_notices', array($this, 'printAdminMessages'))
            ->addAction('save_post', array($this, 'saveTest'), 10, 3)
        ;
    }

    public function renderContentEditorButtons($editorId)
    {
        if ('content' != $editorId) {
            return;
        }
        $this->wp->enqueuePluginStyle('wpt_admin', 'css/admin.css');

        $this->output('Test/Editor/content-editor-buttons');
    }

    public function renderEditQuestions(WP_Post $item)
    {
        $this->wp->enqueuePluginStyle('wpt_admin', 'css/admin.css');
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/edit-questions', array(
            'scales'      => $test->buildScalesWithRange(),
            'questions'   => $test->buildQuestions(),
            'prefix'      => $test->getQuestionsPrefix(),
            'scorePrefix' => $test->getScorePrefix(),
            'isWarnOfSettings' => $test->isWarnOfSettings(),
        ));
    }

    public function renderAddQuestions(WP_Post $item)
    {
        $this->wp->enqueuePluginStyle('wpt_admin', 'css/admin.css');
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/add-questions', array(
            'addNewCount' => 10,
            'startFrom'   => $test->buildQuestions()->count(),
            'scales'      => $test->buildScales(),
            'prefix'      => $test->getQuestionsPrefix(),
        ));
    }

    public function renderEditFormulas(WP_Post $item)
    {
        $this->wp
            ->enqueuePluginStyle('wpt_admin', 'css/admin.css')
            ->enqueuePluginScript('field_selection', 'js/vendor/kof/field-selection.js', array('jquery'), false, true)
            ->enqueuePluginScript('wpt_test_edit_formulas', 'js/test-edit-formulas.js', array('jquery'), false, true)
        ;
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/edit-formulas', array(
            'results'    => $test->buildResults(),
            'variables'  => $test->buildFormulaVariables(),
            'prefix'     => $test->getFormulasPrefix(),
        ));
    }

    public function saveTest($id, WP_Post $item, $isUpdate)
    {
        $test = new WpTesting_Model_Test($item);
        if (!$test->getId()) {
            return;
        }
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
}
