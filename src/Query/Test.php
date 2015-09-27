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
     * @param integer $id
     * @param string $name
     * @return WpTesting_Model_Test
     */
    public function findByIdOrName($id, $name)
    {
        return $this->findFirstByParams(array(
            'ID=|post_name=' => array($id, $name),
        ));
    }

    /**
     * @param array $orderBy
     * @return fRecordSet|WpTesting_Model_Test[]
     */
    public function findAll(array $orderBy = array())
    {
        return $this->findAllByParams(array(), $orderBy);
    }

    /**
     * @param array $orderBy
     * @return fRecordSet|WpTesting_Model_Test[]
     */
    public function findAllPublished(array $orderBy = array(), $limit = null)
    {
        return $this->findAllByParams(array(
            'post_status='  => 'publish',
        ), $orderBy, $limit);
    }

    public function findAllByIds(array $ids = array())
    {
        if (empty($ids)) {
            $ids = array(-1);
        }

        return $this->findAllByParams(array(
            'ID='  => $ids,
        ), array(
            'FIELD(ID, ' . implode(', ', $ids) . ')' => 'ASC',
        ));
    }

    /**
     * @param integer $respondentId
     * @param array $orderBy
     * @return fRecordSet|WpTesting_Model_Test[]
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
     * @return fRecordSet|WpTesting_Model_Test[]
     */
    protected function findAllByParams(array $where = array(), array $orderBy = array(), $limit = null)
    {
        return fRecordSet::build($this->modelName, array(
            'post_type='    => 'wpt_test',
        ) + $where, $orderBy, $limit);
    }

    /**
     * @param array $where
     * @param array $orderBy
     * @return WpTesting_Model_Test
     * @throws fNoRemainingException When test is not found
     */
    protected function findFirstByParams(array $where = array(), array $orderBy = array())
    {
        $result = fRecordSet::build($this->modelName, array(
            'post_type='    => 'wpt_test',
        ) + $where, $orderBy, $limit = 1);
        return $result->getRecord(0);
    }
}