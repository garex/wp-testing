<?php
class WpTesting_Query_Passing extends WpTesting_Query_AbstractQuery
{
    /**
     * @return WpTesting_Query_Passing
     */
    public static function create($className = __CLASS__)
    {
        return parent::create($className);
    }

    public function findAllPagedSorted($page, $recordsPerPage = 10, $orderBy)
    {
        return fRecordSet::build($this->modelName, array(), $orderBy, $recordsPerPage, $page);
    }

}