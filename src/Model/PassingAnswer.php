<?php

/**
 * @method integer getQuestionId() getId() Gets the current value of question_id
 * @method WpTesting_Model_PassingAnswer setQuestionId() setQuestionId(integer $id) Sets the value for question_id
 */
class WpTesting_Model_PassingAnswer extends WpTesting_Model_AbstractModel
{

    public function __construct($key=null)
    {
        $values = array();
        if (is_array($key) && in_array(null, $key)) {
            $values = $key;
            $key    = null;
        }
        parent::__construct($key);
        if (!empty($values)) {
            $this->loadFromResult(new ArrayIterator(array($values)), true);
        }
    }

    /**
     * @return WpTesting_Model_Question
     */
    public function createQuestion()
    {
        return $this->createWpTesting_Model_Question();
    }

    /**
     * @return WpTesting_Model_Answer
     */
    public function createAnswer()
    {
        return $this->createWpTesting_Model_Answer();
    }
}
