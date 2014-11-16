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
 */
class WpTesting_Model_Test extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'id'        => 'ID',
        'title'     => 'post_title',
        'created'   => 'post_date',
        'modified'  => 'post_modified',
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
        $answers   = $this->buildAnswers();
        $questions = $this->buildWpTesting_Model_Questions();
        if (count($answers)) {
            foreach ($questions as $question) { /* @var $question WpTesting_Model_Question */
                $question->setAnswers($answers);
            }
        }
        return $questions;
    }

    /**
     * @return WpTesting_Model_Scale[]
     */
    public function buildScales()
    {
        return fRecordSet::build('WpTesting_Model_Scale', array(
            'term_id=' => $this->getTermIdFromFilteredTaxonomies('wpt_scale'),
        ));
    }

    /**
     * Build scales and setup their ranges from test's questions
     *
     * @return WpTesting_Model_Scale[]
     */
    public function buildScalesWithRange()
    {
        $questionIds   = array_filter($this->listWpTesting_Model_Questions());
        $questionIds[] = 0;
        $questionIds   = implode(',', $questionIds);
        $scales      = $this->buildScales();
        $scoresTable = fORM::tablize('WpTesting_Model_Score');
        foreach ($scales as $scale) {
            /* @var $db fDatabase */
            $db     = fORMDatabase::retrieve('WpTesting_Model_Score', 'read');
            $result = $db->translatedQuery('
                SELECT
                    SUM(IF(score_value > 0, 0, score_value)) AS total_negative,
                    SUM(IF(score_value > 0, score_value, 0)) AS total_positive
                FROM ' . $scoresTable . '
                WHERE TRUE
                    AND question_id IN (' . $questionIds . ')
                    AND scale_id    = ' . intval($scale->getId()) . '
                GROUP BY scale_id
                HAVING total_negative < total_positive
            ');
            if ($result->countReturnedRows()) {
                $values = $result->fetchRow();
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
        $results = fRecordSet::build('WpTesting_Model_Result', array(
            'term_id=' => $this->getTermIdFromFilteredTaxonomies('wpt_result'),
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
     * @return WpTesting_Model_Answer[]
     */
    public function buildAnswers()
    {
        return fRecordSet::build('WpTesting_Model_Answer', array(
            'term_id=' => $this->getTermIdFromFilteredTaxonomies('wpt_answer'),
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
        return $ids;
    }

    protected function getQuestionsPrefix()
    {
        return fORMRelated::determineRequestFilter('WpTesting_Model_Test', 'WpTesting_Model_Question', 'test_id');
    }

    protected function getScoresPrefix()
    {
        return $this->getQuestionsPrefix() .
            fORMRelated::determineRequestFilter('WpTesting_Model_Question', 'WpTesting_Model_Score', 'question_id');
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

    protected function hasAnswers()
    {
        return $this->buildAnswers()->count() > 0;
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
            && $this->hasAnswers()
            && $this->hasScales()
            && $this->hasWpTesting_Model_Questions()
        ;

        $questions = fRecordSet::build('WpTesting_Model_Question', array(
            'test_id=' => $this->getId(),
        ));
        if (!$questions->count()) {
            return false;
        }
        /* @var $question WpTesting_Model_Question */
        $question = $questions->getRecord(0);
        return $question->hasWpTesting_Model_Scores();
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
        $answersCount = count($questions[0]->getAnswers());
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
        $scoresPrefix    = $this->getScoresPrefix();
        $formulasPrefix  = $this->getFormulasPrefix();
        $isAssoc         = true;
        $request        += array(
            'wpt_question_title' => array(),
            'wpt_score_value'    => array(),
            'wpt_formula_source' => array(),
        );

        foreach ($request['wpt_question_title'] as $key => $value) {
            $key = json_decode(stripslashes($key), $isAssoc);
            $request[$questionsPrefix . 'question_id']    [$key['i']] = $key['id'];
            $request[$questionsPrefix . 'question_title'] [$key['i']] = $value;
        }

        foreach ($request['wpt_score_value'] as $key => $value) {
            $key = json_decode(stripslashes($key), $isAssoc);
            $request[$scoresPrefix . 'answer_id']   [$key['i']][$key['j']] = $key['answer_id'];
            $request[$scoresPrefix . 'scale_id']    [$key['i']][$key['j']] = $key['scale_id'];
            $request[$scoresPrefix . 'score_value'] [$key['i']][$key['j']] = $value;
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
}
