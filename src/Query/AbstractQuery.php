<?php
abstract class WpTesting_Query_AbstractQuery
{

    protected $modelName = null;

    protected function __construct()
    {
        $this->modelName = str_replace('_Query_', '_Model_', get_class($this));
    }

    public static function create($className = __CLASS__)
    {
        return new $className();
    }

    /**
     * @return WpTesting_Model_AbstractModel[]
     */
    public function findAll()
    {
        return fRecordSet::build($this->modelName);
    }

}