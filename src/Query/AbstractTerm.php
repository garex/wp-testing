<?php
abstract class WpTesting_Query_AbstractTerm extends WpTesting_Query_AbstractQuery
{

    /**
     * @return string Current term taxonomy for filters
     */
    abstract protected function getTaxonomy();

    /**
     * @param string $name
     * @return WpTesting_Model_AbstractTerm
     * @throws fNotFoundException
     */
    public function findByName($name)
    {
        return $this->findByParams(array(
            'name=' => $name,
        ));
    }

    /**
     * @param array $where
     * @return WpTesting_Model_AbstractTerm
     * @throws fNotFoundException
     */
    protected function findByParams(array $where = array())
    {
        $taxonomyTable = fORM::tablize('WpTesting_Model_Taxonomy');
        try {
            return fRecordSet::build($this->modelName, array(
                $taxonomyTable . '.taxonomy=' => $this->getTaxonomy(),
            ) + $where)->getRecord(0);
        } catch (fNoRemainingException $e) {
            throw new fNotFoundException($this->modelName . ' not found by conditions: ' . var_export($where, true));
        }
    }
}
