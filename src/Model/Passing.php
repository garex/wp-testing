<?php

/**
 * @method integer getId() Gets the current value of id
 * @method WpTesting_Model_Passing setId(integer $id) Sets the value for id
 * @method integer getTestId() Gets the current value of test_id
 * @method WpTesting_Model_Passing setTestId(integer $id) Sets the value for test_id
 * @method fTimestamp getCreated() Gets the current value of created
 * @method WpTesting_Model_Passing setCreated(fTimestamp|string $created) Sets the value for created
 * @method fTimestamp getModified() Gets the current value of modified
 * @method WpTesting_Model_Passing setModified(fTimestamp|string $modified) Sets the value for modified
 * @method integer getIp() Gets the current value of ip
 * @method WpTesting_Model_Passing setIp(string $ip) Sets the value for ip
 * @method integer getDeviceUuid() Gets the current value of device's uuid
 * @method WpTesting_Model_Passing setDeviceUuid(string $uuid) Sets the value for device's uuid
 * @method integer getUserAgent() Gets the current value of user agent
 * @method WpTesting_Model_Passing setUserAgent(string $userAgent) Sets the value for user agent
 * @method integer getRespondentId() Gets the current value of respondent id
 * @method string getStatus() Gets the current value of status
 * @method WpTesting_Model_Passing setStatus(string $status) Sets the value for status
 * @method fRecordSet|WpTesting_Model_Answer[] buildAnswersOnce() Gets passing's answers with cache
 */
class WpTesting_Model_Passing extends WpTesting_Model_AbstractParent
{

    /**
     * Passing is public and viewable by everyone
     */
    const STATUS_PUBLISHED = 'publish';

    /**
     * Passing is in trash and can be restored or deleted
     */
    const STATUS_TRASHED = 'trash';

    /**
     * @var fRecordSet|WpTesting_Model_Scale[]
     */
    protected $scalesWithRange = null;

    protected $columnAliases = array(
        'id'            => 'passing_id',
        'status'        => 'passing_status',
        'created'       => 'passing_created',
        'modified'      => 'passing_modified',
        'ip'            => 'passing_ip',
        'device_uuid'   => 'passing_device_uuid',
        'user_agent'    => 'passing_user_agent',
    );

    /**
     * @var WpTesting_Component_StepStrategy
     */
    private $stepStrategy;

    public function __construct($key = null, $salt = null)
    {
        if (is_string($key) && preg_match('/^([a-z0-9]+)[a-f0-9]{32}$/i', $key, $matches)) {
            $id  = base_convert($matches[1], 36, 10);
            $key = ($this->generateSlug($id, $salt) == $key) ? $id : null;
        }
        return parent::__construct($key);
    }

    /**
     * @param WpTesting_Model_Test $test
     * @return WpTesting_Model_Passing
     */
    public function populateFromTest(WpTesting_Model_Test $test)
    {
        $now = time();
        return $this
            ->setCreated($now)
            ->setModified($now)
            ->setTestId($test->getId())
            ->populate(true)
            ->linkRelated('WpTesting_Model_Answers')
        ;
    }

    /**
     * Saves passing and all objects related to it.
     *
     * @throws fValidationException
     * @return WpTesting_Model_Passing
     */
    public function storeAll()
    {
        $this->wp->doAction('wp_testing_passing_store_all_before', $this);

        $this
            ->populateAll()
            ->store(true)
        ;

        $this->wp->doAction('wp_testing_passing_store_all_after', $this);
        return $this;
    }

    /**
     * Populates all related objects.
     *
     * @return WpTesting_Model_Passing
     */
    public function populateAll()
    {
        $this->wp->doAction('wp_testing_passing_populate_all_before', $this);

        $_POST = $this->wp->applyFilters('wp_testing_passing_adapt_for_populate', $_POST, $this);
        $this->populate(true);

        $this->wp->doAction('wp_testing_passing_populate_all_after', $this);
        return $this;
    }

    public function isFilled()
    {
        $questionsCount = $this->createTest()->buildQuestions()->count();
        $questionsIds   = array();
        foreach ($this->buildAnswers() as $answer) {
            $questionsIds[] = $answer->getQuestionId();
        }
        return (count(array_unique($questionsIds)) >= $questionsCount);
    }

    public function isTrashed()
    {
        return (self::STATUS_TRASHED == $this->getStatus());
    }

    public function isViewable()
    {
        $viewable = array(self::STATUS_PUBLISHED);
        return ($this->getId() && in_array($this->getStatus(), $viewable));
    }

    public function getSlug($salt = null)
    {
        return $this->generateSlug($this->getId(), $salt);
    }

    /**
     * @param string $postLink
     * @return string
     */
    public function getUrl($postLink = null)
    {
        if (empty($postLink)) {
            $postLink = $this->getWp()->getPostPermalink($this->getTestId());
        }
        $postLink       = rtrim($postLink, '/&');
        $slug           = $this->getSlug($this->getWp()->getSalt());
        $hasQueryString = !is_null(parse_url($postLink, PHP_URL_QUERY));
        $postLink      .= ($hasQueryString) ? '&wpt_passing_slug=' . $slug : '/' . $slug . '/';
        return $postLink;
    }

    /**
     * Sets the value for respondent id
     *
     * @param integer $respondentId
     * @return WpTesting_Model_Passing
     */
    public function setRespondentId($respondentId)
    {
        if (empty($respondentId)) {
            $respondentId = null;
        }
        return $this->set('respondent_id', $respondentId);
    }

    public function setStepStrategy(WpTesting_Component_StepStrategy $stepStrategy)
    {
        $this->stepStrategy = $stepStrategy;
        return $this;
    }

    /**
     * @return WpTesting_Model_Step
     */
    public function getCurrentStep()
    {
        return $this->stepStrategy->getCurrentStep();
    }

    /**
     * @return string
     */
    public function getStepsCounter()
    {
        return $this->stepStrategy->getStepsCounter();
    }

    /**
     * @return integer
     */
    public function getQuestionsCount()
    {
        return $this->stepStrategy->getQuestionsCount();
    }

    /**
     * @return integer
     */
    public function getAnsweredQuestionsCount()
    {
        return $this->stepStrategy->getAnsweredQuestionsCount();
    }

    /**
     * @return fRecordSet|WpTesting_Model_Answer[]
     */
    public function buildAnswers()
    {
        return $this->buildRelated('WpTesting_Model_Answers');
    }

    /**
     * @return fRecordSet|WpTesting_Model_Score[]
     */
    public function buildAnswersScores()
    {
        $result = array();
        foreach ($this->buildAnswersOnce() as $answer) {
            foreach ($answer->buildScores() as $score) {
                $result[] = $score;
            }
        }
        return fRecordSet::buildFromArray('WpTesting_Model_Score', array_values($result));
    }

    /**
     * Build scales and setup their ranges from test's questions
     *
     * @return fRecordSet|WpTesting_Model_Scale[]
     */
    public function buildScalesWithRange()
    {
        $result = array();
        foreach ($this->createTest()->buildScalesWithRange() as $testScale) {
            $scale = clone $testScale;
            $result[$testScale->getId()] = $testScale;
        }

        $scoresByScales = array_fill_keys(array_keys($result), 0);
        foreach ($this->buildAnswersScores() as $score) {
            $scoresByScales[$score->getScaleId()] += $score->getValue();
        }

        foreach ($result as $id => $scale) {
            $scale->setValue($scoresByScales[$id]);
        }

        $records = fRecordSet::buildFromArray('WpTesting_Model_Scale', array_values($result));
        if ($this->createTest()->isSortScalesByScore()) {
            $records = $records->sort('getValue', 'desc');
        }
        return $records;
    }

    /**
     * Build scales and setup their ranges from test's questions.
     * Cached version.
     *
     * @return fRecordSet|WpTesting_Model_Scale[]
     */
    public function buildScalesWithRangeOnce()
    {
        if (is_null($this->scalesWithRange)) {
            $this->scalesWithRange = $this->buildScalesWithRange();
        }
        return $this->scalesWithRange;
    }

    /**
     * Prepare results through test, that has true formulas, using current test variables
     *
     * @return fRecordSet|WpTesting_Model_Result[]
     */
    public function buildResults()
    {
        $test      = $this->createTest();
        $variables = $test->buildFormulaVariables($this);
        $result    = array();
        foreach ($test->buildResults() as $testResult) {
            $formula = $testResult->getFormula();
            $formula->resetValues();
            foreach ($variables as $variable) {
                $formula->addValue($variable->getSource(), $variable->getValue(), $variable->getValueAsRatio());
            }
            if ($formula->isTrue()) {
                $result[] = $formula->createResult();
            }
        }
        return fRecordSet::buildFromArray('WpTesting_Model_Result', $result);
    }

    /**
     * @return WpTesting_Model_Test
     */
    public function createTest()
    {
        return $this->createRelated('WpTesting_Model_Test')->setWp($this->getWp());
    }

    public function trash()
    {
        return $this->changeStatus(self::STATUS_TRASHED);
    }

    public function publish()
    {
        return $this->changeStatus(self::STATUS_PUBLISHED);
    }

    protected function changeStatus($to)
    {
        return $this->setModified(time())->setStatus($to)->store();
    }

    /**
     * @param integer $id
     * @param string $salt
     * @return string
     */
    protected function generateSlug($id, $salt = null)
    {
        return base_convert($id, 10, 36) . md5($salt . $id);
    }
}
