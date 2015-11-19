<?php
class WpTesting_Query_Blog extends WpTesting_Query_AbstractQuery
{

    /**
     * @return WpTesting_Query_Blog
     */
    public static function create($className = __CLASS__)
    {
        return parent::create($className);
    }

    /**
     * @return fRecordSet|WpTesting_Model_Blog[]
     */
    public function findAll()
    {
        return fRecordSet::build($this->modelName);
    }
}
