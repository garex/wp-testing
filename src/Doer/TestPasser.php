<?php

abstract class WpTesting_Doer_TestPasser extends WpTesting_Doer_AbstractDoer
{

    /**
     * Initially we show to respondent form with test description, questions and answers
     */
    const ACTION_FILL_FORM = 'fill-form';

    /**
     * After form filled and button clicked, we process it and redirect to passing result
     */
    const ACTION_PROCESS_FORM = 'process-form';

    /**
     * After form processed and redirected, we show results page with scales of concrete passing
     */
    const ACTION_GET_RESULTS = 'get-results';

    /**
     * @var WpTesting_Model_Test
     */
    protected $test = null;

    /**
     * @var WpTesting_Model_Passing
     */
    private $passing = null;

    /**
     * @var WpTesting_Doer_TestPasserAction
     */
    private $actionProcessor = null;

    /**
     * @param WpTesting_Model_Test $test
     * @throws UnexpectedValueException
     * @return self
     */
    protected function beforeRender(WpTesting_Model_Test $test)
    {
        $this->test = $test;
        $action     = $this->getTestPassingAction();
        $isLive     = (self::ACTION_FILL_FORM == $action || $this->test->isFinal());
        if (!$isLive) {
            throw new UnexpectedValueException(sprintf('Test %d is under construction', $test->getId()));
        }

        $this->registerScripts()->wp->addFilter('body_class', array($this, 'addPassingActionCssClass'));
        $this->createActionProcessor($action)->beforeRender($this->test, $this->passing);
        $this->enqueueStyle('public');
        return $this;
    }

    public function addPassingActionCssClass($classes)
    {
        $classes[] = 'wpt_test-' . $this->getTestPassingAction();
        return $classes;
    }

    /**
     * @param string $content
     * @return string
     */
    public function renderTestContent($content)
    {
        $action   = $this->getTestPassingAction();
        $template = $this->wp->locateTemplate('entry-content-wpt-test-' . $action . '.php');
        $template = ($template) ? $template : 'Test/Passer/' . $action;

        $this->wp->doAction('wp_testing_passer_render_content',             $this->test);
        $this->wp->doAction('wp_testing_passer_render_content_' . $action,  $this->test);

        return $this->createActionProcessor($action)->renderContent($content, $template);
    }

    private function getTestPassingAction()
    {
        if ($this->wp->getQuery()->get('wpt_passing_slug')) {
            return self::ACTION_GET_RESULTS;
        }
        $this->passing = new WpTesting_Model_Passing();
        $this->passing->setWp($this->wp);
        if ($this->isPost()) {
            $this->passing->populateFromTest($this->test);
            if (self::ACTION_PROCESS_FORM == $this->getRequestValue('passer_action')) {
                return self::ACTION_PROCESS_FORM;
            }
        }
        return self::ACTION_FILL_FORM;
    }

    /**
     * @param string $action
     * @return WpTesting_Doer_TestPasserAction
     */
    private function createActionProcessor($action)
    {
        if (is_null($this->actionProcessor)) {
            switch ($action) {
                case self::ACTION_FILL_FORM:
                    $this->actionProcessor = new WpTesting_Doer_TestPasserAction_FillForm($this->wp);
                    break;
                case self::ACTION_PROCESS_FORM:
                    $this->actionProcessor = new WpTesting_Doer_TestPasserAction_ProcessForm($this->wp);
                    break;
                case self::ACTION_GET_RESULTS:
                    $this->actionProcessor = new WpTesting_Doer_TestPasserAction_GetResults($this->wp);
                    break;
            }
        }
        return $this->actionProcessor;
    }
}
