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
     * @return WpTesting_Model_Test[]
     */
    public function findAll()
    {
        return $this->findAllByParams();
    }

    /**
     * @return WpTesting_Model_Test[]
     */
    public function findAllPublished()
    {
        return $this->findAllByParams(array(
            'post_status='  => 'publish',
        ));
    }

    /**
     * @param integer $respondentId
     * @return WpTesting_Model_Test[]
     */
    public function findAllByPassingRespondent($respondentId)
    {
        return fRecordSet::buildFromSQL($this->modelName, array(
            implode(PHP_EOL, array(
                'SELECT DISTINCT Test.* FROM %r AS Test',
                'JOIN %r AS Passing ON Passing.test_id = Test.ID AND Passing.respondent_id = %i',
                'WHERE Test.post_type = %s',
            )),
            array(
                $this->tableName,
                fORM::tablize('WpTesting_Model_Passing'),
                $respondentId,
                'wpt_test',
            ),
        ));
    }

    /**
     * @param array $where
     * @return WpTesting_Model_Test[]
     */
    protected function findAllByParams(array $where = array())
    {
        return fRecordSet::build($this->modelName, array(
            'post_type='    => 'wpt_test',
        ) + $where);
    }
}