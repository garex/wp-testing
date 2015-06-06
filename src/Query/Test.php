<?php
class WpTesting_Query_Test extends WpTesting_Query_AbstractQuery
{

    /**
     * @return WpTesting_Query_Test
     */
    public static function create($className = __CLASS__)
    {
        return parent::create($className);
    }

    /**
     * @param array $orderBy
     * @return WpTesting_Model_Test[]
     */
    public function findAll(array $orderBy = array())
    {
        return $this->findAllByParams(array(), $orderBy);
    }

    /**
     * @param array $orderBy
     * @return WpTesting_Model_Test[]
     */
    public function findAllPublished(array $orderBy = array())
    {
        return $this->findAllByParams(array(
            'post_status='  => 'publish',
        ), $orderBy);
    }

    /**
     * @param integer $respondentId
     * @param array $orderBy
     * @return WpTesting_Model_Test[]
     */
    public function findAllByPassingRespondent($respondentId, array $orderBy = array('Test.ID'))
    {
        return fRecordSet::buildFromSQL($this->modelName, array(
            implode(PHP_EOL, array(
                'SELECT DISTINCT Test.* FROM %r AS Test',
                'JOIN %r AS Passing ON Passing.test_id = Test.ID AND Passing.respondent_id = %i',
                'WHERE Test.post_type = %s',
                'ORDER BY %s',
            )),
            array(
                $this->tableName,
                fORM::tablize('WpTesting_Model_Passing'),
                $respondentId,
                'wpt_test',
                implode(',', $orderBy)
            ),
        ));
    }

    /**
     * @param array $where
     * @param array $orderBy
     * @return WpTesting_Model_Test[]
     */
    protected function findAllByParams(array $where = array(), array $orderBy = array())
    {
        return fRecordSet::build($this->modelName, array(
            'post_type='    => 'wpt_test',
        ) + $where, $orderBy);
    }
}