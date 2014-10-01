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
     * @return WpTesting_Query_Test[]
     */
    public function findAll()
    {
        return $this->findAllByParams();
    }

    /**
     * @return WpTesting_Query_Test[]
     */
    public function findAllPublished()
    {
        return $this->findAllByParams(array(
            'post_status='  => 'publish',
        ));
    }

    /**
     * @param array $where
     * @return WpTesting_Query_Test[]
     */
    protected function findAllByParams(array $where = array())
    {
        return fRecordSet::build($this->modelName, array(
            'post_type='    => 'wpt_test',
        ) + $where);
    }
}