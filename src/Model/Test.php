<?php
/**
 * @method integer getId() getId() Gets the current value of id
 * @method WpTesting_Model_Test setId() setId(integer $id) Sets the value for id
 * @method string getTitle() getTitle() Gets the current value of title
 * @method WpTesting_Model_Test setTitle() setTitle(string $title) Sets the value for title
 * @method fTimestamp getCreated() getCreated() Gets the current value of created
 * @method WpTesting_Model_Test setCreated() setCreated(fTimestamp|string $created) Sets the value for created
 * @method fTimestamp getModified() getModified() Gets the current value of modified
 * @method WpTesting_Model_Test setModified() setModified(fTimestamp|string $modified) Sets the value for modified
 * @method string getContent() getContent() Gets the current value of content
 * @method string getStatus() getStatus() Gets the current value of status
 */
class WpTesting_Model_Test extends WpTesting_Model_AbstractModel
{

    /**
     * Test is public and viewable by everyone
     */
    const STATUS_PUBLISHED = 'publish';

    protected $columnAliases = array(
        'id'        => 'ID',
        'title'     => 'post_title',
        'created'   => 'post_date',
        'modified'  => 'post_modified',
        'content'   => 'post_content',
        'status'    => 'post_status',
    );

    /**
     * @var WpTesting_Model_Taxonomy[]
     */
    protected $taxonomies = null;

    public function __construct($key = null)
    {
        if (is_object($key) && isset($key->post_type)) {
            if ($key->post_type != 'wpt_test') {
                $this->values['ID'] = null;
                return;
            }
            $postAsArray = (array)$key;
            unset($postAsArray['ancestors']);
            unset($postAsArray['filter']);
            unset($postAsArray['format_content']);
            return parent::__construct(new ArrayIterator(array($postAsArray)));
        }
        return parent::__construct($key);
    }

    /**
     * @return WpTesting_Model_Question[]
     */
    public function buildQuestions()
    {
        return $this->buildWpTesting_Model_Questions();
    }

    /**
     * @return WpTesting_Model_Scale[]
     */
    public function buildScales()
    {
        $ids = $this->getTermIdFromFilteredTaxonomies('wpt_scale');
        return fRecordSet::build('WpTesting_Model_Scale', array(
            'term_id=' => $ids,
        ), array(
            'FIELD(term_id, ' . implode(', ', $ids) . ')' => 'asc',
        ));
    }

    /**
     * Build scales and setup their ranges from test's questions
     *
     * @return WpTesting_Model_Scale[]
     */
    public function buildScalesWithRange()
    {
        $scales = $this->buildScales();
        if (!$scales->count()) {
            return $scales;
        }
        $questionIds = array_filter($this->listWpTesting_Model_Questions());
        if (empty($questionIds)) {
            return $scales;
        }
        /* @var $db fDatabase */
        $db           = fORMDatabase::retrieve('WpTesting_Model_Score', 'read');
        $scoresTable  = fORM::tablize('WpTesting_Model_Score');
        $answersTable = fORM::tablize('WpTesting_Model_Answer');
        $result       = $db->translatedQuery('
            SELECT
                scale_id,
                SUM(IF(score_value > 0, 0, score_value)) AS total_negative,
                SUM(IF(score_value > 0, score_value, 0)) AS total_positive
            FROM ' . $scoresTable  . ' AS s
            JOIN ' . $answersTable . ' AS a ON s.answer_id = a.answer_id
            WHERE TRUE
                AND question_id IN (' . implode(',', $questionIds) . ')
                AND scale_id    IN (' . implode(',', $scales->getPrimaryKeys()) . ')
            GROUP BY scale_id
            HAVING total_negative < total_positive
        ');
        $resultByPk = array();
        foreach ($result->fetchAllRows() as $row) {
            $resultByPk[$row['scale_id']] = $row;
        }
        foreach ($scales as $scale) {
            if (isset($resultByPk[$scale->getId()])) {
                $values = $resultByPk[$scale->getId()];
                $scale->setRange($values['total_negative'], $values['total_positive']);
            }
        }
        return $scales;
    }

    /**
     * @return WpTesting_Model_Result[]
     */
    public function buildResults()
    {
        $ids = $this->getTermIdFromFilteredTaxonomies('wpt_result');
        $results = fRecordSet::build('WpTesting_Model_Result', array(
            'term_id=' => $this->getTermIdFromFilteredTaxonomies('wpt_result'),
        ), array(
            'FIELD(term_id, ' . implode(', ', $ids) . ')' => 'asc',
        ));

        /* @var $result WpTesting_Model_Result */
        foreach ($results as $result) {
            $result->setTest($this);
        }

        return $results;
    }

    /**
     * @return WpTesting_Model_FormulaVariable[]
     */
    public function buildFormulaVariables($scalesWithRange = null)
    {
        $variables = array();
        if (is_null($scalesWithRange)) {
            $scalesWithRange = $this->buildScalesWithRange();
        }
        foreach ($scalesWithRange as $scale) {
            $variables[] = new WpTesting_Model_FormulaVariable($scale);
        }
        return $variables;
    }

    /**
     * @return WpTesting_Model_Formula[]
     */
    public function buildFormulas()
    {
        return $this->buildWpTesting_Model_Formulas();
    }

    /**
     * @return WpTesting_Model_GlobalAnswer[]
     */
    public function buildGlobalAnswers()
    {
        $ids = $this->getTermIdFromFilteredTaxonomies('wpt_answer');
        return fRecordSet::build('WpTesting_Model_GlobalAnswer', array(
            'term_id=' => $ids,
        ), array(
            'FIELD(term_id, ' . implode(', ', $ids) . ')' => 'asc',
        ));
    }

    /**
     * @return WpTesting_Model_Taxonomy[]
     */
    protected function buildTaxonomies()
    {
        return $this->buildWpTesting_Model_Taxonomy();
    }

    /**
     * @return fRecordSet of WpTesting_Model_Taxonomy
     */
    protected function buildTaxonomiesOnce()
    {
        if (is_null($this->taxonomies)) {
            $this->taxonomies = $this->buildTaxonomies();
        }
        return $this->taxonomies;
    }

    /**
     * Filter related taxonomies and return term id from they
     * @param string $taxonomy One of wpt_answer|wpt_scale|wpt_result|wpt_category
     * @return array
     */
    protected function getTermIdFromFilteredTaxonomies($taxonomy)
    {
        $ids = array();
        foreach ($this->buildTaxonomiesOnce()->filter(array('getTaxonomy=' => $taxonomy)) as $taxonomy) {
            $ids[] = $taxonomy->getTermId();
        }
        return (count($ids) == 0) ? array(-1) : $ids;
    }

    protected function getQuestionsPrefix()
    {
        return fORMRelated::determineRequestFilter('WpTesting_Model_Test', 'WpTesting_Model_Question', 'test_id');
    }


    protected function getAnswersPrefix()
    {
        return $this->getQuestionsPrefix() .
            fORMRelated::determineRequestFilter('WpTesting_Model_Question', 'WpTesting_Model_Answer', 'question_id');
    }

    protected function getScoresPrefix()
    {
        return $this->getAnswersPrefix() .
            fORMRelated::determineRequestFilter('WpTesting_Model_Answer', 'WpTesting_Model_Score', 'answer_id');
    }

    protected function getFormulasPrefix()
    {
        return fORMRelated::determineRequestFilter('WpTesting_Model_Test', 'WpTesting_Model_Formula', 'test_id');
    }

    /**
     * Can respondent use this test to get results?
     *
     * Final test is test, that have scores.
     * Scores can be only, when test have questions, answers and scales.
     * Results are good to have but not required: they are humanize "scientific" language
     * of scales to more understandable words.
     *
     * @return boolean
     */
    public function isFinal()
    {
        foreach ($this->buildScalesWithRange() as $scale) {
            if ($scale->getMaximum()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can respondent see this test currently?
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->getStatus() == self::STATUS_PUBLISHED;
    }

    protected function hasAnswers()
    {
        if ($this->buildGlobalAnswers()->count() > 0) {
            return true;
        }

        // Find both global and individual answers
        $questionIds = fRecordSet::build('WpTesting_Model_Question', array(
            'test_id=' => $this->getId(),
        ))->getPrimaryKeys();

        return fRecordSet::tally('WpTesting_Model_Answer', array(
            'question_id=' => $questionIds,
        )) > 0;
    }

    protected function hasScales()
    {
        return $this->buildScales()->count() > 0;
    }

    /**
     * Can scores be editable?
     *
     * @return boolean
     */
    public function canEditScores()
    {
        return true
            && $this->hasScales()
            && $this->hasWpTesting_Model_Questions()
            && $this->hasAnswers()
        ;
    }

    /**
     * @return array
     */
    public function getMemoryWarnSettings()
    {
        $iniKeys = array(
            'max_input_vars',
            'suhosin.get.max_vars',
            'suhosin.post.max_vars',
            'suhosin.request.max_vars',
        );
        $values = array();
        foreach ($iniKeys as $iniKey) {
            $value = ini_get($iniKey);
            if ($value !== false) {
                $values[$iniKey] = (int)$value;
            }
        }
        return $values;
    }

    /**
     * @see http://stackoverflow.com/questions/10303714/php-max-input-vars
     * @return boolean
     */
    public function isWarnOfSettings()
    {
        $scalesCount = count($this->buildScales());
        if (!$scalesCount) {
            return false;
        }
        $questions      = $this->buildQuestions();
        $questionsCount = count($questions);
        if (!$questionsCount) {
            return false;
        }
        $answersCount = count($questions[0]->buildAnswers());
        if (!$answersCount) {
            return false;
        }

        $possibleInputsCount = 0
            + WpTesting_Model_Question::ADD_NEW_COUNT
            + $questionsCount
            + $scalesCount * $questionsCount * $answersCount
        ;

        return $possibleInputsCount > (min($this->getMemoryWarnSettings()) - 150);
    }

    /**
     * Unpack request for subsequent population from it to ORM naming standards
     *
     * @param array $input
     * @return array
     */
    public function adaptForPopulate($request)
    {
        $questionsPrefix = $this->getQuestionsPrefix();
        $answersPrefix   = $this->getAnswersPrefix();
        $scoresPrefix    = $this->getScoresPrefix();
        $formulasPrefix  = $this->getFormulasPrefix();
        $isAssoc         = true;
        $request        += array(
            'wpt_question_title'              => array(),
            'wpt_answer_title'                => array(),
            'wpt_question_individual_answers' => array(),
            'wpt_score_value'                 => array(),
            'wpt_formula_source'              => array(),
        );

        foreach ($request['wpt_question_title'] as $key => $value) {
            $key = json_decode(stripslashes($key), $isAssoc);
            $request[$questionsPrefix . 'question_id']    [$key['q']] = $key['id'];
            $request[$questionsPrefix . 'question_title'] [$key['q']] = $value;
        }

        foreach ($request['wpt_answer_title'] as $key => $value) {
            $key = json_decode(stripslashes($key), $isAssoc);
            $request[$answersPrefix . 'answer_id']    [$key['q']][$key['a']] = $key['id'];
            $request[$answersPrefix . 'answer_title'] [$key['q']][$key['a']] = $value;
        }

        foreach ($request['wpt_question_individual_answers'] as $key => $value) {
            $key = json_decode(stripslashes($key), $isAssoc);

            $value = trim($value);
            if ($value == '') {
                continue;
            }

            $titles = preg_split('/[\r\n]+/', $value);
            foreach ($titles as $title) {
                $title = trim(preg_replace('/^\w{1,3}[^\w\s]\s+/', '', $title));
                $request[$answersPrefix . 'answer_id']    [$key['q']][] = null;
                $request[$answersPrefix . 'answer_title'] [$key['q']][] = $title;
            }
        }

        foreach ($request['wpt_score_value'] as $key => $value) {
            $key = json_decode(stripslashes($key), $isAssoc);
            $request[$scoresPrefix . 'answer_id']   [$key['q']][$key['a']][$key['s']] = $key['answer_id'];
            $request[$scoresPrefix . 'scale_id']    [$key['q']][$key['a']][$key['s']] = $key['scale_id'];
            $request[$scoresPrefix . 'score_value'] [$key['q']][$key['a']][$key['s']] = $value;
        }

        foreach ($request['wpt_formula_source'] as $key => $value) {
            $key = json_decode(stripslashes($key), $isAssoc);
            $request[$formulasPrefix . 'test_id']        [$key['i']] = $key['test_id'];
            $request[$formulasPrefix . 'formula_id']     [$key['i']] = $key['formula_id'];
            $request[$formulasPrefix . 'result_id']      [$key['i']] = $key['result_id'];
            $request[$formulasPrefix . 'formula_source'] [$key['i']] = $value;
        }

        return $request;
    }

    /**
     * @param bool $isRecursive
     * @return WpTesting_Model_Test
     */
    public function populateQuestions($isRecursive = false)
    {
        $this->populateWpTesting_Model_Questions($isRecursive);
        $table     = fORM::tablize('WpTesting_Model_Question');
        $questions =& $this->related_records[$table]['test_id']['record_set'];
        $questions = $questions->filter(array('getTitle!=' => ''));
        return $this;
    }

    /**
     * @param bool $isRecursive
     * @return WpTesting_Model_Test
     */
    public function populateFormulas($isRecursive = false)
    {
        return $this->populateWpTesting_Model_Formulas($isRecursive);
    }

    /**
     * Synchronize individual answers with global in each question.
     *
     * Remove not existing global answers.
     * Remove empty-title individual answers.
     * Create not yet existing answers from global answers.
     */
    public function syncQuestionsAnswers()
    {
        /** @var fRecordSet $globalAnswers */
        $globalAnswers    = $this->buildGlobalAnswers();
        $globalAnswersIds = $globalAnswers->call('getId');
        $globalAnswerSort = array_flip($globalAnswersIds);
        $globalAnswerSort[null] = 100;
        foreach ($this->buildQuestions() as $question) {
            $existingGlobalAnswersIds = array();
            foreach ($question->buildAnswers() as $answer) {
                if ($answer->isDeletable($globalAnswersIds)) {
                    $answer->delete();
                } else {
                    $existingGlobalAnswersIds[] = $answer->getGlobalAnswerId();
                    $newAnswerSort = $globalAnswerSort[$answer->getGlobalAnswerId()];
                    if ($answer->getSort() != $newAnswerSort) {
                        $answer->setSort($newAnswerSort)->store();
                    }
                }
            }

            // Create not yet existing answers from global answers.
            $createGlobalAnswersIds = array_diff($globalAnswersIds, $existingGlobalAnswersIds);
            foreach ($createGlobalAnswersIds as $globalAnswerId) {
                $answer = new WpTesting_Model_Answer();
                $answer->setGlobalAnswerId($globalAnswerId);
                $answer->setQuestionId($question->getId());
                $answer->setSort($globalAnswerSort[$answer->getGlobalAnswerId()]);
                $answer->store();
            }
        }
    }

    /**
     * Export as WP native content entity object
     *
     * @return WP_Post
     */
    public function toWpPost()
    {
        if (class_exists('WP_Post')) {
            $post = new WP_Post(new stdClass());
        } else {
            $post = new stdClass();
        }
        $post->filter = 'raw';
        foreach ($this->values as $key => $value) {
            $post->$key = (string)$value;
        }
        return $post;
    }

    protected function configure()
    {
        fORMRelated::setOrderBys(
            $this,
            'WpTesting_Model_Taxonomy',
            array(WP_DB_PREFIX . 'term_relationships.`term_order`' => 'asc')
        );
    }
}
