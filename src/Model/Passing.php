<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method WpTesting_Model_Passing setId() setId(integer $id) Sets the value for id
 * @method integer getTestId() getId() Gets the current value of test_id
 * @method WpTesting_Model_Passing setTestId() setId(integer $id) Sets the value for test_id
 * @method fTimestamp getCreated() getCreated() Gets the current value of created
 * @method WpTesting_Model_Passing setCreated() setCreated(fTimestamp|string $created) Sets the value for created
 * @method fTimestamp getModified() getModified() Gets the current value of modified
 * @method WpTesting_Model_Passing setModified() setModified(fTimestamp|string $modified) Sets the value for modified
 * @method integer getIp() getIp() Gets the current value of ip
 * @method WpTesting_Model_Passing setIp() setIp(string $ip) Sets the value for ip
 * @method integer getDeviceUuid() getDeviceUuid() Gets the current value of device's uuid
 * @method WpTesting_Model_Passing setDeviceUuid() setDeviceUuid(string $uuid) Sets the value for device's uuid
 * @method integer getUserAgent() getUserAgent() Gets the current value of user agent
 * @method WpTesting_Model_Passing setUserAgent() setUserAgent(string $userAgent) Sets the value for user agent
 * @method integer getRespondentId() getRespondentId() Gets the current value of respondent id
 * @method string getStatus() getStatus() Gets the current value of status
 * @method WpTesting_Model_Passing setStatus() setStatus(string $status) Sets the value for status
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
     * @var WpTesting_Model_Scale[]
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
    public function populate(WpTesting_Model_Test $test)
    {
        $this->setCreated(time())->setModified(time())->setTestId($test->getId());
        parent::populate(true);
        $this->linkWpTesting_Model_Answers();
        return $this;
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
        parent::populate(true);

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
        return parent::set('respondent_id', $respondentId);
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
     * @return WpTesting_Model_Answer[]
     */
    public function buildAnswers()
    {
        return $this->buildWpTesting_Model_Answers();
    }

    /**
     * Build scales and setup their ranges from test's questions
     *
     * @return WpTesting_Model_Scale[]
     */
    public function buildScalesWithRange()
    {
        $result = array();
        foreach ($this->createTest()->buildScalesWithRange() as $testScale) {
            $scale = clone $testScale;
            $result[$testScale->getId()] = $testScale;
        }

        $scoresByScales = array_fill_keys(array_keys($result), 0);
        foreach ($this->buildAnswers() as $answer) {
            $scores = $answer->buildScores();
            foreach ($scores as $score) { /* @var $score WpTesting_Model_Score */
                $scoresByScales[$score->getScaleId()] += $score->getValue();
            }
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
     * @return WpTesting_Model_Scale[]
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
     * @return WpTesting_Model_Result[]
     */
    public function buildResults()
    {
        $test      = $this->createTest();
        $variables = $test->buildFormulaVariables($this->buildScalesWithRangeOnce());
        $result    = array();
        foreach ($test->buildFormulas() as $formula) {
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
        return $this->createWpTesting_Model_Test()->setWp($this->getWp());
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
