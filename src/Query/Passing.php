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
        $conditions = array();
        foreach ($params as $key => $value) {
            if ($value == '-') {
                if ('user' == $key) {
                    $key = 'respondent_id';
                }
                $conditions[$key . '='] = array(null, '');
                continue;
            }
            if ('passing_created' == $key) {
                if (strlen($value) != 6) {
                    continue;
                }
                $year   = intval(substr($value, 0, 4));
                $month  = intval(substr($value, 4));
                $day    = 1;
                $format = '%04s-%02s-%02s';
                $conditions[$key . '>='] = sprintf($format, $year, $month, $day);
                if ($month < 12) {
                    $month++;
                } else {
                    $year++;
                    $month = 1;
                }
                $conditions[$key . '<']  = sprintf($format, $year, $month, $day);
            } elseif ('user' == $key) {
                $key   = 'respondent_id';
                $value = fRecordSet::build('WpTesting_Model_Respondent', array(
                    'user_login|user_nicename|user_email|display_name~' => $value,
                ))->getPrimaryKeys();
                $conditions[$key . '='] = $value;
            } elseif ('passing_ip' == $key) {
                $conditions[$key . '^~'] = $value;
            } elseif ('passing_user_agent' == $key) {
                $conditions[$key . '~'] = $value;
            } else {
                $conditions[$key . '='] = $value;
            }
        }
        return fRecordSet::build($this->modelName, $conditions, $orderBy, $recordsPerPage, $page);
    }

    /**
     * @param array $ids
     * @param array $orderBy
     * @return fRecordSet
     */
    public function findAllByIds($ids, $orderBy = array())
    {
        return fRecordSet::build($this->modelName, array(
            'passing_id=' => $ids,
        ), $orderBy);
    }

    /**
     * Passings sorted by the order of provided $ids
     * @param array $ids
     * @return fRecordSet
     */
    public function findAllByIdsSorted($ids)
    {
        $orderBy = (!empty($ids)) ? array('FIELD(passing_id, ' . implode(', ', $ids) . ')' => 'asc') : array();
        return $this->findAllByIds($ids, $orderBy);
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

    /**
     * @return fResult
     */
    public function countAllStatuses()
    {
        return $this->db->translatedQuery('
            SELECT passing_status, COUNT(*) AS passing_count
            FROM %r
            GROUP BY passing_status
        ', $this->tableName);
    }
}