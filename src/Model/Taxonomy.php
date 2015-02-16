<?php

/**
 * WordPress needed model for category-like entities
 *
 * @method string getDescription() getDescription() Gets the current value of description
 */
class WpTesting_Model_Taxonomy extends WpTesting_Model_AbstractModel
{

    /**
     * Helper function to workaround old WP versions
     * @param integer $testId
     * @param array $termIds
     * @return array
     */
    public function sortTermIdsByTermOrder($testId, $termIds)
    {
        if (empty($testId) || empty($termIds)) {
            return $termIds;
        }

        /* @var $db fDatabase */
        $db = fORMDatabase::retrieve(__CLASS__, 'read');
        $records = $db->translatedQuery('
            SELECT
                tt.term_id
            FROM
                ' . WP_DB_PREFIX . 'term_relationships tr
            JOIN
                ' . WP_DB_PREFIX . 'term_taxonomy tt
                ON tt.term_taxonomy_id = tr.term_taxonomy_id
            WHERE tt.term_id IN (' . implode(', ', array_map('intval', $termIds)) . ')
            AND tr.object_id = ' . intval($testId) . '
            ORDER BY tr.term_order
        ');
        $result = array();
        foreach ($records as $record) {
            $result[] = $record['term_id'];
        }
        return $result;
    }
}
