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

    /**
     * @return fResult
     */
    public function queryAllMonths()
    {
        return $this->queryAllMonthsByRespondent(0);
    }

    /**
     * @param integer $respondentId
     * @return fResult
     */
    public function queryAllMonthsByRespondent($respondentId)
    {
        return $this->db->translatedQuery('
            SELECT DISTINCT YEAR(passing_created) AS created_year, MONTH(passing_created) AS created_month
            FROM %r
            WHERE (respondent_id = %i OR %i = 0)
            ORDER BY passing_id
        ', $this->tableName, $respondentId, $respondentId);
    }
}