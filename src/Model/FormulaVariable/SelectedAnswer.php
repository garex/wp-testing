<?php

class WpTesting_Model_FormulaVariable_SelectedAnswer extends WpTesting_Model_FormulaVariable implements WpTesting_Model_FormulaVariable_IAllBuilder
{

    public static function buildAllFrom(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing = null)
    {
        $variables = array();

        if (is_null($passing)) {
            $answeredIds = array();
        } else {
            $answeredIds = $passing->buildAnswersOnce()->getPrimaryKeys();
            if (count($answeredIds)) {
                $answeredIds = array_combine($answeredIds, $answeredIds);
            }
        }
        $titleFormat = __('Question %1$s answer %2$s', 'wp-testing');
        $typeLabel   = __('Answer Variable', 'wp-testing');
        foreach ($test->buildQuestionsWithAnswers() as $q => $question) {
            foreach ($question->buildAnswers() as $a => $answer) {
                $variable       = new self();
                $questionNumber = $q + 1;
                $answerNumber   = $a + 1;
                $value          = isset($answeredIds[$answer->getId()]) ? 1 : 0;
                $variable
                    ->setTitle(sprintf($titleFormat, $questionNumber, $answerNumber))
                    ->setTypeLabel($typeLabel)
                    ->setSource(sprintf('question_%1$s_answer_%2$s', $questionNumber, $answerNumber))
                    ->setValue($value)
                    ->setValueAsRatio($value)
                ;
                $variables[$variable->getSource()] = $variable;
            }
        }

        return $variables;
    }
}