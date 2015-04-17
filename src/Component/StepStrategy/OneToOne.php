<?php

/**
 * One question per step
 */
class WpTesting_Component_StepStrategy_OneToOne extends WpTesting_Component_StepStrategy
{

    protected function fillSteps()
    {
        $questions = $this->test->buildQuestions();
        $total     = $questions->count();
        $answered  = $this->answeredQuestions->count();
        foreach ($questions as $q => $question) {
            $records   = fRecordSet::buildFromArray('WpTesting_Model_Question', array($q => $question));
            $isCurrent = ($answered == $q);
            $this->addStep(new WpTesting_Model_Step('', $records), $isCurrent);
        }
        return $this
            ->enableStepsCounter()
        ;
    }

}