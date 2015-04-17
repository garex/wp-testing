<?php

/**
 * All test's questions in single step
 */
class WpTesting_Component_StepStrategy_AllInOne extends WpTesting_Component_StepStrategy
{

    protected function fillSteps()
    {
        return $this->addStep(
            new WpTesting_Model_Step('', $this->test->buildQuestions())
        );
    }

}