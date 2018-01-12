<?php
/**
 * @method integer getId() Gets the current value of id
 * @method integer getIdOnce() Gets cached value of id
 * @method WpTesting_Model_Test setId(integer $id) Sets the value for id
 * @method string getTitle() Gets the current value of title
 * @method WpTesting_Model_Test setTitle(string $title) Sets the value for title
 * @method fTimestamp getCreated() Gets the current value of created
 * @method WpTesting_Model_Test setCreated(fTimestamp|string $created) Sets the value for created
 * @method fTimestamp getModified() Gets the current value of modified
 * @method WpTesting_Model_Test setModified(fTimestamp|string $modified) Sets the value for modified
 * @method WpTesting_Model_Test setContent(string $content) Sets the value for content
 * @method string getContent() Gets the current value of content
 * @method string getStatus() Gets the current value of status
 * @method WpTesting_Model_Test setExcerpt(string $excerpt) Sets the value for excerpt
 * @method string getExcerpt() Gets the current value of excerpt
 * @method WpTesting_Model_Test setContentFiltered(string $contentFiltered) Sets the value for content filtered
 * @method string getContentFiltered() Gets the current value of content filtered
 * @method WpTesting_Model_Test setToPing(string $toPing) Sets the value for URLs that should be pinged
 * @method string getToPing() Gets the current value for URLs that should be pinged
 * @method WpTesting_Model_Test setPinged(string $pinged) Sets the value for URLs that already pinged
 * @method string getPinged() Gets the current value for URLs that already pinged
 * @method WpTesting_Model_Test setType(string $type) Sets the value for type that should be wpt_test
 * @method string getType() Gets the current value for type
 * @method WpTesting_Model_Test setName(string $name) Sets the value for name (url unique part)
 * @method string getName() Gets the current value for name (url unique part)
 * @method integer getAuthorId() Gets the current value for author's id
 * @method WpTesting_Model_Test setAuthorId(integer $authorId) Sets the current value for author's id
 * @method WpTesting_Model_Scale[] buildScalesWithRangeOnce() Build scales and setup their ranges from test's questions
 * @method WpTesting_Model_Formula[] buildFormulasOnce() @see WpTesting_Model_Test::buildFormulas
 */
class WpTesting_Model_Test extends WpTesting_Model_AbstractParent
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
        'content_filtered' => 'post_content_filtered',
        'status'    => 'post_status',
        'excerpt'   => 'post_excerpt',
        'type'      => 'post_type',
        'name'      => 'post_name',
        'author_id' => 'post_author',
    );

    /**
     * @var fRecordSet|WpTesting_Model_Taxonomy[]
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

            $columns = $this->getColumnsAsMethodsOnce($this);
            foreach (array_keys($postAsArray) as $column) {
                if (!isset($columns[$column])) {
                    unset($postAsArray[$column]);
                }
            }

            $key = new ArrayIterator(array($postAsArray));
        }
        parent::__construct($key);
    }

    /**
     * @return fRecordSet|WpTesting_Model_Question[]
     */
    public function buildQuestions()
    {
        return $this->me()->buildRelated('WpTesting_Model_Questions');
    }

    /**
     * @return fRecordSet|WpTesting_Model_Question[]
     */
    public function buildQuestionsWithAnswersAndScores()
    {
        $questions   = $this->me()->buildQuestions();
        if (!count($questions)) {
            return $questions;
        }
        if ($this->me()->hasRelatedIn($questions, 'WpTesting_Model_Answer')) {
            return $questions;
        }
        $answersById = $this->associateManyRelated($questions,   'WpTesting_Model_Answer', 'question_id');
        $this->associateManyRelated($answersById, 'WpTesting_Model_Score',  'answer_id');
        return $questions;
    }

    /**
     * @return fRecordSet|WpTesting_Model_Question[]
     */
    public function buildQuestionsWithAnswers()
    {
        $questions   = $this->me()->buildQuestions();
        if (!count($questions)) {
            return $questions;
        }
        if ($this->me()->hasRelatedIn($questions, 'WpTesting_Model_Answer')) {
            return $questions;
        }
        $this->associateManyRelated($questions,   'WpTesting_Model_Answer', 'question_id');
        return $questions;
    }

    /**
     * @return fRecordSet|WpTesting_Model_Scale[]
     */
    public function buildScales()
    {
        return $this->buildRelatedTaxonomies('wpt_scale', 'WpTesting_Model_Scale');
    }

    /**
     * Build scales and setup their ranges from test's questions
     *
     * @return fRecordSet|WpTesting_Model_Scale[]
     */
    public function buildScalesWithRange()
    {
        $scales = $this->buildScales();
        if (!$scales->count()) {
            return $scales;
        }
        $questionIds = array_filter($this->listRelated('WpTesting_Model_Questions'));
        if (empty($questionIds)) {
            return $scales;
        }
        $lastColumnInRow = ($this->isMultipleAnswers()) ? 's.answer_id' : 'question_id';
        /* @var $db fDatabase */
        $db           = fORMDatabase::retrieve('WpTesting_Model_Score', 'read');
        $scoresTable  = fORM::tablize('WpTesting_Model_Score');
        $answersTable = fORM::tablize('WpTesting_Model_Answer');
        $result       = $db->translatedQuery('
            SELECT
                scale_id,
                SUM(minimum_in_row) AS minimum_in_column,
                SUM(maximum_in_row) AS maximum_in_column,
                SUM(sum_in_row)     AS sum_in_column
            FROM (
                SELECT
                    scale_id,
                    MIN(IF(score_value > 0, 0, score_value)) AS minimum_in_row,
                    MAX(IF(score_value > 0, score_value, 0)) AS maximum_in_row,
                    SUM(score_value)                         AS sum_in_row
                FROM ' . $scoresTable  . ' AS s
                JOIN ' . $answersTable . ' AS a ON s.answer_id = a.answer_id
                WHERE TRUE
                    AND question_id IN (' . implode(',', $questionIds) . ')
                    AND scale_id    IN (' . implode(',', $scales->getPrimaryKeys()) . ')
                GROUP BY scale_id, question_id, ' . $lastColumnInRow . '
                HAVING minimum_in_row < maximum_in_row
            ) AS groupped
            GROUP BY scale_id
        ');
        $resultByPk = array();
        foreach ($result->fetchAllRows() as $row) {
            $resultByPk[$row['scale_id']] = $row;
        }
        foreach ($scales as $scale) {
            if (isset($resultByPk[$scale->getId()])) {
                $values = $resultByPk[$scale->getId()];
                $scale->setRange($values['minimum_in_column'], $values['maximum_in_column'], $values['sum_in_column']);
            }
        }
        return $scales;
    }

    /**
     * @return fRecordSet|WpTesting_Model_Result[]
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
     * @param WpTesting_Model_Passing $passing
     * @return WpTesting_Model_FormulaVariable[]
     */
    public function buildPublicFormulaVariables(WpTesting_Model_Passing $passing = null)
    {
        $variables = array();

        $variables += WpTesting_Model_FormulaVariable_ScaleValue::buildAllFrom($this, $passing);

        return $this->wp->applyFilters('wp_testing_test_build_public_formula_variables', $variables, $this, $passing);
    }

    /**
     * @param WpTesting_Model_Passing $passing
     * @return WpTesting_Model_FormulaVariable[]
     */
    public function buildFormulaVariables(WpTesting_Model_Passing $passing = null)
    {
        $variables = $this->buildPublicFormulaVariables($passing);

        $variables += WpTesting_Model_FormulaVariable_SelectedAnswer::buildAllFrom($this, $passing);

        return $this->wp->applyFilters('wp_testing_test_build_formula_variables', $variables, $this, $passing);
    }

    /**
     * @return fRecordSet|WpTesting_Model_Formula[]
     */
    public function buildFormulas()
    {
        return $this->buildRelated('WpTesting_Model_Formulas');
    }

    /**
     * @return fRecordSet|WpTesting_Model_GlobalAnswer[]
     */
    public function buildGlobalAnswers()
    {
        return $this->buildRelatedTaxonomies('wpt_answer', 'WpTesting_Model_GlobalAnswer');
    }

    protected function buildRelatedTaxonomies($taxomony, $model)
    {
        $ids = $this->getTermIdFromFilteredTaxonomies($taxomony);
        return fRecordSet::build($model, array(
            'term_id=' => $ids,
        ), array(
            'FIELD(term_id, ' . implode(', ', $ids) . ')' => 'asc',
        ));
    }

    /**
     * @return fRecordSet|WpTesting_Model_Taxonomy[]
     */
    protected function buildTaxonomies()
    {
        return $this->buildRelated('WpTesting_Model_Taxonomy');
    }

    /**
     * @return fRecordSet|WpTesting_Model_Taxonomy[]
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
     * Adds new question associated to this test
     * @param string $title
     * @return self
     */
    public function addQuestion($title)
    {
        $question = new WpTesting_Model_Question();
        $question->setTitle($title);
        $this->associateRelated('WpTesting_Model_Questions', $this->buildQuestions()->merge($question));
        return $this;
    }

    public function associateScale(WpTesting_Model_Scale $scale)
    {
        return $this->associateAbstractTerm($scale);
    }

    public function associateGlobalAnswer(WpTesting_Model_GlobalAnswer $globalAnswer)
    {
        return $this->associateAbstractTerm($globalAnswer);
    }

    private function associateAbstractTerm(WpTesting_Model_AbstractTerm $term)
    {
        $this->associateRelated('WpTesting_Model_Taxonomies',
            $this->buildRelated('WpTesting_Model_Taxonomies')
                ->merge($term->createTaxonomy())
        );
        return $this;
    }

    /**
     * Can respondent use this test to get results?
     *
     * Final test is test, that have scores or at least one non-empty formula.
     * Scores can be only, when test have questions, answers and scales.
     * Results are good to have but not required: they are humanize "scientific" language
     * of scales to more understandable words.
     *
     * @return boolean
     */
    public function isFinal()
    {
        foreach ($this->buildScalesWithRangeOnce() as $scale) {
            if ($scale->getLength()) {
                return true;
            }
        }

        // If we have at least one result with formula — we assume, that it was added legally
        foreach ($this->buildFormulasOnce() as $formula) {
            if (!$formula->isEmpty()) {
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

    public function isMultipleAnswers()
    {
        return $this->isOptionEnabled('wpt_test_page_multiple_answers');
    }

    public function isResetAnswersOnBack()
    {
        return $this->isOptionEnabled('wpt_test_page_reset_answers_on_back');
    }

    public function isShowProgressMeter()
    {
        return $this->isOptionEnabled('wpt_test_page_show_progress_meter');
    }

    public function isShowScales()
    {
        return $this->isOptionEnabled('wpt_result_page_show_scales');
    }

    public function isShowScalesDiagram()
    {
        return $this->isOptionEnabled('wpt_result_page_show_scales_diagram');
    }

    public function isShowTestDescription()
    {
        return $this->isOptionEnabled('wpt_result_page_show_test_description');
    }

    public function isSortScalesByScore()
    {
        return $this->isOptionEnabled('wpt_result_page_sort_scales_by_score');
    }

    public function isOneQuestionPerStep()
    {
        return $this->isOptionEnabled('wpt_test_page_one_question_per_step');
    }

    protected function isOptionEnabled($key, $default = null)
    {
        return $this->isOptionEqual($key, 1, $default);
    }

    protected function isOptionEqual($key, $expectedValue, $defaultValue = null)
    {
        if (!$this->getId()) {
            return null;
        }
        $actualValue = $this->getWp()->getPostMeta($this->getId(), $key, true);
        if ('' === $actualValue && !is_null($defaultValue)) {
            $actualValue = $defaultValue;
        }
        return ($expectedValue == $actualValue);
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
     * Saves all objects related to test.
     *
     * @throws fValidationException
     * @return WpTesting_Model_Test
     */
    public function storeAll()
    {
        $this->transactionStart();
        $this->wp->doAction('wp_testing_test_store_all_before', $this);

        $this->buildQuestionsWithAnswersAndScores();
        fORMValidation::disableForeignKeyConstraintsCheck();

        $this
            ->populateAll()
            ->store(true)
            ->syncQuestionsAnswers()
        ;

        $this->wp->doAction('wp_testing_test_store_all_after', $this);
        $this->transactionFinish();

        return $this;
    }

    /**
     * Populates all related objects.
     *
     * @return WpTesting_Model_Test
     */
    public function populateAll()
    {
        $this->wp->doAction('wp_testing_test_populate_all_before', $this);

        $_POST = $this->adaptForPopulate($_POST, $this->getId());
        $this
            ->populateQuestions(true)
            ->populateFormulas()
        ;
        $this->wp->doAction('wp_testing_test_populate_all_after', $this);

        return $this;
    }

    /**
     * Unpack request for subsequent population from it to ORM naming standards
     *
     * @param array $request
     * @param int $testId
     * @return array
     */
    public function adaptForPopulate($request, $testId)
    {
        $questionsPrefix = $this->getQuestionsPrefix();
        $answersPrefix   = $this->getAnswersPrefix();
        $scoresPrefix    = $this->getScoresPrefix();
        $formulasPrefix  = $this->getFormulasPrefix();
        $request        += array(
            'wpt_score_value'    => array(),
            'wpt_formula_source' => array(),
        );

        $json = json_decode(stripslashes($request['wpt_questions_answers_json']), $assoc = true);
        foreach ($json as $q => $question) {
            $request[$questionsPrefix . 'question_id']    [$q] = $question['id'];
            $request[$questionsPrefix . 'question_title'] [$q] = $question['title'];
            foreach ($question['answers'] as $a => $answer) {
                $request[$answersPrefix . 'answer_id']        [$q][$a] = $answer['id'];
                $request[$answersPrefix . 'answer_title']     [$q][$a] = $answer['title'];
                $request[$answersPrefix . 'global_answer_id'] [$q][$a] = $answer['global_answer_id'];
                $s = -1;
                foreach ($answer['scores'] as $scaleId => $value) {
                    $s++;
                    if (!$value) {
                        continue;
                    }
                    $request[$scoresPrefix . 'answer_id']   [$q][$a][$s] = $answer['id'];
                    $request[$scoresPrefix . 'scale_id']    [$q][$a][$s] = $scaleId;
                    $request[$scoresPrefix . 'score_value'] [$q][$a][$s] = $value;
                }
            }
        }

        $json = json_decode(stripslashes($request['wpt_formulas_json']), $assoc = true);
        foreach ($json as $resultId => $value) {
            $request[$formulasPrefix . 'test_id']        [$resultId] = $testId;
            $request[$formulasPrefix . 'formula_id']     [$resultId] = $value['id'];
            $request[$formulasPrefix . 'result_id']      [$resultId] = $resultId;
            $request[$formulasPrefix . 'formula_source'] [$resultId] = $value['source'];
        }

        return $this->wp->applyFilters('wp_testing_test_adapt_for_populate', $request, $testId, $this);
    }

    /**
     * @param bool $isRecursive
     * @return WpTesting_Model_Test
     */
    public function populateQuestions($isRecursive = false)
    {
        $this->populateRelated('WpTesting_Model_Questions', $isRecursive);
        $table   = fORM::tablize('WpTesting_Model_Question');
        $records =& $this->related_records[$table]['test_id']['record_set'];
        $records = $records->filter(array('getTitle!=' => ''));
        return $this;
    }

    /**
     * @param bool $isRecursive
     * @return WpTesting_Model_Test
     */
    public function populateFormulas($isRecursive = false)
    {
        return $this->populateRelated('WpTesting_Model_Formulas', $isRecursive);
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
                $question->associateAnswers(array($answer));
            }
        }
    }

    /**
     * Gets test' public URL when it visible to public
     *
     * @return string
     */
    public function getPublishedUrl()
    {
        return $this->isPublished() ? $this->wp->getPermalink($this->toWpPost()) : null;
    }

    /**
     * Export as WP native content entity object
     *
     * @return WP_Post|stdClass
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

    public function getQuestionsCount()
    {
        return count($this->buildQuestions());
    }

    public function getMaxAnswersCount()
    {
        $maxCount = 0;

        foreach ($this->buildQuestionsWithAnswers() as $question) {
            $maxCount = max($maxCount, count($question->buildAnswers()));
        }

        return $maxCount;
    }

    protected function configure()
    {
        $relationshipsTable = fORM::tablize('WpTesting_Model_Relationship');
        fORMRelated::setOrderBys(
            $this,
            'WpTesting_Model_Taxonomy',
            array($relationshipsTable . '.`term_order`' => 'asc')
        );
    }
}
