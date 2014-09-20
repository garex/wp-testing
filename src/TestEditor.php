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
        $this->wp->addMetaBox('wpt_questions', 'Questions', array($this, 'renderQuestions'), 'wpt_test');
    }

    public function renderQuestions(WP_Post $item)
    {
        $this->wp->enqueuePluginStyle('wpt_admin', 'css/admin.css');
        $test = new WpTesting_Model_Test($item);
        $this->output('Test/Editor/questions', array(
            'answers'     => $test->buildAnswers(),
            'scales'      => $test->buildScales(),
            'questions'   => $test->buildQuestions(),
            'prefix'      => $test->getQuestionsPrefix(),
            'addNewCount' => 10,
        ));
    }

    public function saveTest($id, WP_Post $item, $isUpdate)
    {
        $test = new WpTesting_Model_Test($item);
        if (!$test->getId()) {
            return;
        }
        $test->populateQuestions();
        $test->store();
    }

}
