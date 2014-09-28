<?php

class WpTesting_Doer_TestEditor extends WpTesting_Doer_AbstractDoer
{

    public function customizeUi()
    {
        $this->wp
            ->addMetaBox('wpt_edit_questions', 'Edit Questions',    array($this, 'renderEditQuestions'), 'wpt_test')
            ->addMetaBox('wpt_add_questions',  'Add New Questions', array($this, 'renderAddQuestions'),  'wpt_test')
            ->addAction('save_post', array($this,  'saveTest'), 10, 3)
        ;
    }

    public function renderEditQuestions(WP_Post $item)
    {
        $this->wp->enqueuePluginStyle('wpt_admin', 'css/admin.css');
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/edit-questions', array(
            'scales'      => $test->buildScales(),
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

    public function saveTest($id, WP_Post $item, $isUpdate)
    {
        $test = new WpTesting_Model_Test($item);
        if (!$test->getId()) {
            return;
        }
        $test->populateQuestions(true);
        $test->store(true);
    }

}
