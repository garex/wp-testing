<?php
abstract class WpTesting_Query_AbstractQuery
{

    protected $modelName = null;

    protected $tableName = null;

    protected $db = null;

    protected function __construct()
    {
        $this->modelName = str_replace('_Query_', '_Model_', get_class($this));
        $this->tableName = fORM::tablize($this->modelName);
        $this->db        = fORMDatabase::retrieve($this->modelName, 'read');
    }

    public static function create($className = __CLASS__)
    {
        return new $className();
    }

    /**
     * @return fRecordSet|WpTesting_Model_AbstractModel[]
     */
    public function findAll()
    {
        return fRecordSet::build($this->modelName);
    }

    /**
     * Translates one SQL statement using fSQLTranslation and executes it
     *
     * @param string $sql
     * @return fResult
     * @throws BadMethodCallException
     */
    protected function singleTranslatedQuery($sql)
    {
        $arguments    = func_get_args();
        $arguments[0] = $sql;
        $result       = call_user_func_array(array($this->db, 'translatedQuery'), $arguments);
        if ($result instanceof fResult) {
            return $result;
        }
        if (is_array($result) && isset($result[0]) && $result[0] instanceof fResult) {
            return $result[0];
        }
        throw new BadMethodCallException('Result of translatedQuery is not fRecordSet: ' . var_export($result, true));
    }
}
