<?php

class WpTesting_Model_Passing extends WpTesting_Model_AbstractModel
{

    /**
     * @param WpTesting_Model_Test $test
     * @return WpTesting_Model_Passing
     */
    public function populate(WpTesting_Model_Test $test)
    {
        $this->setTestId($test->getId());
        parent::populate(true);

        $answers = $this->buildAnswers();
        foreach ($test->buildQuestions() as $i => $question) {
            $answers[$i]->setQuestionId($question->getId());
        }

        return $this;
    }

    /**
     * @return WpTesting_Model_PassingAnswer[]
     */
    public function buildAnswers()
    {
        return $this->buildWpTesting_Model_PassingAnswers();
    }

    /**
     * @return WpTesting_Model_Scales[]
     */
    public function calculateScalesTotals()
    {
        $results = array();

        foreach ($this->buildAnswers() as $passingAnswer) {
            $scores = $passingAnswer->createQuestion()->getScoresByAnswer($passingAnswer->createAnswer());
            foreach ($scores as $score) { /* @var $score WpTesting_Model_Score */
                $scale = $score->createScale();
                if (empty($results[$scale->getId()])) {
                    $results[$scale->getId()] = $scale->resetScore();
                }
                $results[$scale->getId()]->addScore($score);
            }
        }

        if (empty($results)) {
            return $results;
        }

        // Calculate scales global totals (not from respondent answers)
        $globalTotals = array();
        /* @var $test WpTesting_Model_Test */
        $test = $passingAnswer->createQuestion()->createTest();
        foreach ($test->buildQuestions() as $i => $question) {
            foreach ($question->buildScores() as $score) {
                $scale = $score->createScale();
                if (!isset($results[$scale->getId()])) {
                    continue;
                }
                if (!isset($globalTotals[$scale->getId()])) {
                    $globalTotals[$scale->getId()] = $scale->resetScore();
                }
                $globalTotals[$scale->getId()]->addScore($score);
            }
        }

        foreach ($results as $id => $result) {
            $results[$id]->setTotalScale($globalTotals[$id]);
        }

        return $results;
    }

}
