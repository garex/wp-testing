<?php

/**
 * One question per step
 */
class WpTesting_Component_StepStrategy_OneToOne extends WpTesting_Component_StepStrategy
{

    public function fillSteps()
    {
        $questions = $this->test->buildQuestions();
        $total     = $questions->count();
        $answered  = $this->answeredQuestions->count();
        foreach ($questions as $q => $question) {
            $title     = sprintf(__('%1$d out of %2$d', 'wp-testing'), $q + 1, $total);
            $records   = fRecordSet::buildFromArray('WpTesting_Model_Question', array($q => $question));
            $isCurrent = ($answered == $q);
            $this->addStep(new WpTesting_Model_Step($title, $records), $isCurrent);
        }
        return $this;
    }

}