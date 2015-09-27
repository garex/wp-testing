<?php

/**
 * Gets questions of test and transforms them into steps.
 * Where steps include questions. They can include 1 questions, all questions or per sections.
 * Another strategies also possible: not all questions or all.
 *
 * In the result we need all possible steps filled with numbers, titles and questions.
 * Also we need to know current step.
 *
 * To get questions we need test (not just questions from it).
 * To know current step we need all answered questions (and just them — can get from passing).
 */
abstract class WpTesting_Component_StepStrategy
{

    /**
     * @var WpTesting_Model_Test
     */
    protected $test;

    /**
     * @var WpTesting_Model_Question[]
     */
    protected $answeredQuestions;

    /**
     * On start when respondent not yet passed to us any data,
     * answered questions not possible at all.
     *
     * This is different from situation when data passed, but there are no any answers in it.
     *
     * So this will helps to distinguish NULL from 0 in answered questions count.
     *
     * @var boolean
     */
    protected $isAnsweredQuestionsPossible = false;

    /**
     * @var WpTesting_Model_Step[]
     */
    private $steps = array();

    /**
     * @var WpTesting_Model_Step
     */
    private $currentStep = null;

    /**
     * @var boolean
     */
    private $isShowStepsCounter = false;

    public function __construct(WpTesting_Model_Test $test = null, fRecordSet $answeredAnswers = null)
    {
        is_null($test) || $this->setTest($test);
        is_null($answeredAnswers) || $this->extractQuestionsFromAnswers($answeredAnswers);
    }

    /**
     * @param boolean $flag
     * @return self
     */
    public function answeredQuestionsPossible($flag)
    {
        $this->isAnsweredQuestionsPossible = (bool)$flag;
        return $this;
    }

    /**
     * @param WpTesting_Component_StepStrategy $another
     * @throws InvalidArgumentException
     * @return self
     */
    public function fillFrom(WpTesting_Component_StepStrategy $another)
    {
        $this->setTest($another->test);
        $this->answeredQuestions = $another->answeredQuestions;
        $this->isAnsweredQuestionsPossible = $another->isAnsweredQuestionsPossible;

        if (is_null($this->answeredQuestions)) {
            throw new InvalidArgumentException('Empty answered questions provided!');
        }

        return $this;
    }

    /**
     * @return WpTesting_Model_Test
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @return WpTesting_Model_Step
     */
    public function getCurrentStep()
    {
        if (is_null($this->currentStep)) {
            $this->getSteps();
        }
        return $this->currentStep;
    }

    /**
     * @return string
     */
    public function getStepsCounter()
    {
        if (!$this->isShowStepsCounter) {
            return '';
        }
        $step = $this->getCurrentStep();
        if ($step->isLast()) {
            return '';
        }
        return sprintf(__('%1$g out of %2$g', 'wp-testing'), $step->getNumber(), $step->getTotal());
    }

    public function getQuestionsCount()
    {
        return $this->test->buildQuestions()->count();
    }

    public function getAnsweredQuestionsCount()
    {
        return $this->answeredQuestions->count();
    }

    /**
     * @param WpTesting_Model_Test $test
     * @throws InvalidArgumentException
     * @return WpTesting_Component_StepStrategy
     */
    protected function setTest(WpTesting_Model_Test $test)
    {
        $this->test = $test;
        return $this;
    }

    /**
     * @param WpTesting_Model_Step $step
     * @param string $isCurrent
     * @return self
     */
    protected function addStep(WpTesting_Model_Step $step, $isCurrent = true)
    {
        // First step always current
        if (empty($this->steps)) {
            $isCurrent = true;
        }
        $this->steps[] = $step;
        if ($isCurrent) {
            $this->currentStep = $step;
        }
        return $this;
    }

    protected function enableStepsCounter()
    {
        $this->isShowStepsCounter = true;
        return $this;
    }

    /**
     * @return self
     */
    abstract protected function fillSteps();

    /**
     * @return WpTesting_Model_Step[]
     */
    protected function getSteps()
    {
        if (empty($this->steps)) {
            $this->fillSteps();
            if (!count($this->steps)) {
                $emptyQuestions = fRecordSet::buildFromArray('WpTesting_Model_Question', array());
                $this->addStep(new WpTesting_Model_Step('', $emptyQuestions));
            }
            $this->setupTotalsAndNumbers();
        }

        return $this->steps;
    }

    /**
     * @return self
     */
    private function setupTotalsAndNumbers()
    {
        $total = count($this->steps);
        foreach ($this->steps as $i => $step) {
            $step->setTotalAndNumber($total, $i + 1);
        }
        return $this;
    }

    /**
     * @param fRecordSet $answers
     * @return self
     */
    private function extractQuestionsFromAnswers(fRecordSet $answers)
    {
        $questions = array();
        foreach ($answers as $answer) { /* @var $answer WpTesting_Model_Answer */
            $questions[$answer->getQuestionId()] = $answer->createQuestion();
        }
        $this->answeredQuestions = fRecordSet::buildFromArray('WpTesting_Model_Question', $questions);
        return $this;
    }
}