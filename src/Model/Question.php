<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method string getTitle() getTitle() Gets the current value of title
 * @method WpTesting_Model_Question setTitle() setTitle(string $title) Sets the value for title
 */
class WpTesting_Model_Question extends WpTesting_Model_AbstractModel
{

    /**
     * How many items to display in add new box
     */
    const ADD_NEW_COUNT = 10;

    protected $columnAliases = array(
        'title'  => 'question_title',
        'id'     => 'question_id',
    );

    public function populate($recursive = false)
    {
        $this->populateSelf()->populateRelated($recursive);
    }

    protected function populateRelated($recursive = false)
    {
        if ($recursive) {
            $this->populateWpTesting_Model_Answer(true, 'question_id');
        }
        return $this;
    }

    /**
     * @return WpTesting_Model_Answer[]
     */
    public function buildAnswers()
    {
        return $this->buildWpTesting_Model_Answer();
    }

    public function associateAnswers($answers)
    {
        $this->associateWpTesting_Model_Answer($answers);
    }

    /**
     * @return WpTesting_Model_Test
     */
    public function createTest()
    {
        return $this->createWpTesting_Model_Test()->setWp($this->getWp());
    }

    protected function configure()
    {
        fORMRelated::setOrderBys(
            $this,
            'WpTesting_Model_Answer',
            array(
                WPT_DB_PREFIX . 'answers.answer_sort' => 'asc',
                WPT_DB_PREFIX . 'answers.answer_id'   => 'asc',
            )
        );
    }
}
