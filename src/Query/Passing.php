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

    public function findAllPagedSorted($page, $recordsPerPage = 10, $orderBy = array())
    {
        return $this->findAllPagedSortedByParams(array(), $page, $recordsPerPage, $orderBy);
    }

    public function findAllPagedSortedByParams($params, $page, $recordsPerPage = 10, $orderBy = array())
    {
        return fRecordSet::build($this->modelName, $params, $orderBy, $recordsPerPage, $page);
    }
}