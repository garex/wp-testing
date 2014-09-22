<?php

class WpTesting_TestEditor extends WpTesting_Doer
{

    /**
     * @var WpTesting_WordPressFacade
     */
    private $wp = null;

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->wp = $wp;
    }

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
        $this->output('Test/Editor/edit_questions', array(
            'answers'     => $test->buildAnswers(),
            'scales'      => $test->buildScales(),
            'questions'   => $test->buildQuestions(),
            'prefix'      => $test->getQuestionsPrefix(),
            'scorePrefix' => $test->getScorePrefix(),
        ));
    }

    public function renderAddQuestions(WP_Post $item)
    {
        $this->wp->enqueuePluginStyle('wpt_admin', 'css/admin.css');
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/add_questions', array(
            'addNewCount' => 10,
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
