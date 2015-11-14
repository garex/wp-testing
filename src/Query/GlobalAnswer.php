<?php
class WpTesting_Query_GlobalAnswer extends WpTesting_Query_AbstractTerm
{

    protected function getTaxonomy()
    {
        return 'wpt_answer';
    }

    /**
     * @return WpTesting_Query_GlobalAnswer
     */
    public static function create($className = __CLASS__)
    {
        return parent::create($className);
    }

    /**
     * @return WpTesting_Model_GlobalAnswer
     * @throws fNotFoundException
     */
    public function findByName($name)
    {
        return parent::findByName($name);
    }
}
