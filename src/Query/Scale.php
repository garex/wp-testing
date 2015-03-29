<?php
class WpTesting_Query_Scale extends WpTesting_Query_AbstractTerm
{

    protected function getTaxonomy()
    {
        return 'wpt_scale';
    }

    /**
     * @return WpTesting_Query_Scale
     */
    public static function create($className = __CLASS__)
    {
        return parent::create($className);
    }

    /**
     * @return WpTesting_Model_Scale
     * @throws fNotFoundException
     */
    public function findByName($name)
    {
        return parent::findByName($name);
    }
}