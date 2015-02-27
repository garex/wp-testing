<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method integer getGlobalAnswerId() getGlobalAnswerId() Gets the current value of global answer id
 * @method integer getSort() getSort() Gets the current value of sort
 * @method WpTesting_Model_Answer setSort() setSort(integer $sort) Sets the value for sort
 */
class WpTesting_Model_Answer extends WpTesting_Model_AbstractModel
{

    /**
     * Auto-created from test's global answer and have it's title.
     * When global answer gone from test it also will gone.
     */
    const INDIVIDUALITY_GLOBAL = 'global';

    /**
     * Auto-created from test's global answer and have individualized title.
     * Can be transformed into global by clearing title.
     * When global answer gone from test it also will gone.
     */
    const INDIVIDUALITY_INDIVIDUALIZED = 'individualized';

    /**
     * Created in conrete question individually and must has a title.
     * Doesn't related with any global answers.
     * Removed also individually from concrete answer (by simply clearing title).
     */
    const INDIVIDUALITY_INDIVIDUAL = 'individual';

    protected $columnAliases = array(
        'id'    => 'answer_id',
        'title' => 'answer_title',
        'sort'  => 'answer_sort',
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
        return $this->isGlobal() ? $this->createGlobalAnswer()->getTitle() : $this->get('title');
    }

    /**
     * @return string
     */
    public function getIndividualTitle()
    {
        return $this->get('title');
    }

    /**
     * @return string
     */
    public function getGlobalTitle()
    {
        return $this->createGlobalAnswer()->getTitle();
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

    /**
     * @return string One of global, individualized and individual
     */
    public function getIndividuality()
    {
        $globalId = $this->getGlobalAnswerId();
        if (empty($globalId)) {
            return self::INDIVIDUALITY_INDIVIDUAL;
        }

        return ($this->isEmptyTitle())
            ? self::INDIVIDUALITY_GLOBAL
            : self::INDIVIDUALITY_INDIVIDUALIZED
        ;
    }

    /**
     * @return boolean
     */
    public function isEmptyTitle()
    {
        return $this->get('title') == '';
    }

    /**
     * @return boolean
     */
    public function isGlobal()
    {
        return self::INDIVIDUALITY_GLOBAL == $this->getIndividuality();
    }

    /**
     * @return boolean
     */
    public function isIndividual()
    {
        return self::INDIVIDUALITY_INDIVIDUAL == $this->getIndividuality();
    }

    /**
     * Should this answer be deleted?
     *
     * @param array $globalAnswersIds
     * @return boolean
     */
    public function isDeletable($globalAnswersIds)
    {
        return
            // Remove empty-title individual answers
            $this->isIndividual() && $this->isEmptyTitle()
                ||
            // Remove not existing global answers
            $this->getGlobalAnswerId() && !in_array($this->getGlobalAnswerId(), $globalAnswersIds)
        ;
    }

}
