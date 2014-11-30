<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method integer getGlobalAnswerId() getGlobalAnswerId() Gets the current value of global answer id
 */
class WpTesting_Model_Answer extends WpTesting_Model_AbstractModel
{
    protected $columnAliases = array(
        'id'    => 'answer_id',
    );

    /**
     * @var WpTesting_Model_Score[]
     */
    protected $scores = null;

    /**
     * @return string
     */
    public function getTitle()
    {
        $title    = $this->get('answer_title');
        $globalId = $this->getGlobalAnswerId();
        if (empty($title) && !empty($globalId)) {
            return $this->createGlobalAnswer()->getTitle();
        }
        return $title;
    }

    /**
     * @return WpTesting_Model_Score[]
     */
    public function buildScores()
    {
        return $this->buildScoresOnce();
    }

    /**
     * @return fRecordSet of WpTesting_Model_Score
     */
    protected function buildScoresOnce()
    {
        if (is_null($this->scores)) {
            $this->scores = $this->buildWpTesting_Model_Scores();
        }
        return $this->scores;
    }

    /**
     * @return WpTesting_Model_GlobalAnswer
     */
    public function createGlobalAnswer()
    {
        return $this->createWpTesting_Model_GlobalAnswer();
    }

    /**
     * Abbreviration of title
     *
     * @return string
     */
    public function getAbbr()
    {
        return mb_substr($this->getTitle(), 0, 1, 'UTF-8');
    }

    /**
     * Get score anyway (even if it doesn't exists)
     *
     * @param WpTesting_Model_Scale $scale
     * @return WpTesting_Model_Score
     */
    public function getScoreByScale(WpTesting_Model_Scale $scale)
    {
        $result = $this->buildScoresOnce()->filter(array(
            'getScaleId='  => $scale->getId(),
        ));
        if ($result->count()) {
            return $result->getRecord(0);
        }
        return new WpTesting_Model_Score();
    }


}
