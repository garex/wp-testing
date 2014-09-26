<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method string getTitle() getTitle() Gets the current value of title
 */
class WpTesting_Model_Question extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'title'  => 'question_title',
        'id'     => 'question_id',
    );

    /**
     * @var WpTesting_Model_Score[]
     */
    protected $scores = null;

    /**
     * @var WpTesting_Model_Answer[]
     */
    protected $answers = null;

    /**
     * @param fRecordSet $answers
     * @return WpTesting_Model_Question
     */
    public function setAnswers(fRecordSet $answers)
    {
        if (count($answers) == 0 || !($answers[0] instanceof WpTesting_Model_Answer)) {
            return $this;
        }
        $this->answers = $answers;
        return $this;
    }

    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Get score anyway (even if it doesn't exists)
     *
     * @param WpTesting_Model_Answer $answer
     * @param WpTesting_Model_Scale $scale
     * @return WpTesting_Model_Score
     */
    public function getScoreByAnswerAndScale(WpTesting_Model_Answer $answer, WpTesting_Model_Scale $scale)
    {
        $result = $this->buildScoresOnce()->filter(array(
            'getAnswerId=' => $answer->getId(),
            'getScaleId='  => $scale->getId(),
        ));
        if ($result->count()) {
            return $result->getRecord(0);
        }
        return new WpTesting_Model_Score();
    }

    /**
     * Get scores by answer
     *
     * @param WpTesting_Model_Answer $answer
     * @return WpTesting_Model_Score[]
     */
    public function getScoresByAnswer(WpTesting_Model_Answer $answer)
    {
        return $this->buildScoresOnce()->filter(array(
            'getAnswerId=' => $answer->getId(),
        ));
    }

    /**
     * @return fRecordSet of WpTesting_Model_Score
     */
    protected function buildScoresOnce()
    {
        if (is_null($this->scores)) {
            $this->scores = $this->buildWpTesting_Model_Score();
        }
        return $this->scores;
    }

}
