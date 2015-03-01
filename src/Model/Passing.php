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
 */
class WpTesting_Model_Passing extends WpTesting_Model_AbstractModel
{

    /**
     * @var WpTesting_Model_Scale[]
     */
    protected $scalesWithRange = null;

    protected $columnAliases = array(
        'id'    => 'passing_id',
    );

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
