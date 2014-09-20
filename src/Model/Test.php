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
        if ($key instanceof WP_Post) {
            if ($key->post_type != 'wpt_test') {
                return;
            }
            $postAsArray = (array)$key;
            unset($postAsArray['filter']);
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
        return fRecordSet::build('WpTesting_Model_Scale', array(
            'term_id=' => $this->getTermIdFromFilteredTaxonomies('wpt_scale'),
        ));
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

    public function getQuestionsPrefix()
    {
        return fORMRelated::determineRequestFilter('WpTesting_Model_Test', 'WpTesting_Model_Question', 'test_id');
    }

    /**
     * @return WpTesting_Model_Test
     */
    public function populateQuestions()
    {
        $this->populateWpTesting_Model_Questions();
        $table     = fORM::tablize('WpTesting_Model_Question');
        $questions =& $this->related_records[$table]['test_id']['record_set'];
        $questions = $questions->filter(array('getTitle!=' => ''));
        return $this;
    }

}
