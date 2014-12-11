<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method string getTitle() getTitle() Gets the current value of title
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
        return $this->createWpTesting_Model_Test();
    }

}
