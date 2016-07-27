<?php

/**
 * @method integer getId() Gets the current value of id
 * @method string getTitle() Gets the current value of title
 * @method WpTesting_Model_Question setTitle(string $title) Sets the value for title
 */
class WpTesting_Model_Question extends WpTesting_Model_AbstractModel implements JsonSerializable
{

    protected $columnAliases = array(
        'title'  => 'question_title',
        'id'     => 'question_id',
    );

    public function populate($recursive = false)
    {
        $this->populateSelf();
        if ($recursive) {
            $this->populateRelated('WpTesting_Model_Answer', true, 'question_id');
        }
        return $this;
    }

    /**
     * @return fRecordSet|WpTesting_Model_Answer[]
     */
    public function buildAnswers()
    {
        return $this->buildRelated('WpTesting_Model_Answer');
    }

    public function associateAnswers($answers)
    {
        $this->associateRelated('WpTesting_Model_Answer', $answers);
    }

    /**
     * @return WpTesting_Model_Test
     */
    public function createTest()
    {
        return $this->createRelated('WpTesting_Model_Test')->setWp($this->getWp());
    }

    public function jsonSerialize()
    {
        return array(
            'id'        => $this->getId(),
            'title'     => $this->getTitle(),
            'answers'   => $this->buildAnswers()->getRecords(),
        );
    }

    protected function configure()
    {
        $answerTable = fORM::tablize('WpTesting_Model_Answer');
        fORMRelated::setOrderBys(
            $this,
            'WpTesting_Model_Answer',
            array(
                $answerTable . '.answer_sort' => 'asc',
                $answerTable . '.answer_id'   => 'asc',
            )
        );
    }
}
