<?php
/**
 * @method array getColumnsAsMethodsOnce()
 */
abstract class WpTesting_Model_AbstractModel extends fActiveRecord
{

    /**
     * Allows to use column aliases on model, regarding real column values
     *
     * Structure is: array(alias_name => real_name)
     *
     * @var array
     */
    protected $columnAliases = array();

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

    /**
     * @var array
     */
    private $methodCallCache = array();

    /**
     * @var array [Class][Column] => Method
     */
    private static $columnsAsMethodsCache = array();

    /**
     * @var boolean
     */
    private $isTransactionStarted = false;

    public function populate($recursive = false)
    {
        parent::populate($recursive);

        return $this->stripValuesSlashes();
    }

    protected function populateSelf()
    {
        foreach ($this->getColumnsAsMethodsOnce($this) as $column => $method) {
            $isExists = (isset($_POST[$column]) || array_key_exists($column, $_POST));
            if (!$isExists) {
                continue;
            }
            $this->$method($_POST[$column]);
        }
        return $this->stripValuesSlashes();
    }

    /**
     * @param WpTesting_Model_AbstractModel $me
     * @return array
     */
    public static function getColumnsAsMethodsOnce($me)
    {
        $class = get_class($me);
        if (!isset(self::$columnsAsMethodsCache[$class])) {
            $schema = fORMSchema::retrieve($class);
            $table  = fORM::tablize($class);
            self::$columnsAsMethodsCache[$class] = array();
            foreach ($schema->getColumnInfo($table) as $column => $info) {
                self::$columnsAsMethodsCache[$class][$column] = 'set' . fGrammar::camelize($column, true);
            }
        }
        return self::$columnsAsMethodsCache[$class];
    }

    public function exists()
    {
        if (isset($this->columnAliases['id']) && !is_null($this->get('id'))) {
            return true;
        }
        return parent::exists();
    }

    /**
     * Encode complex value that is safe to pass as a part of URI
     *
     * @param mixed $value
     * @return string
     */
    public function encodeSafeUriValue($value)
    {
        return strtr(base64_encode(json_encode($value)), '+/', '-_');
    }

    /**
     * Decode value, encoded by encodeSafeUriValue
     *
     * @see WpTesting_Model_AbstractModel::encodeSafeUriValue
     *
     * @param string $encodedValue
     * @param string $isConvertIntoAssociativeArray
     * @return array|object
     */
    public function decodeSafeUriValue($encodedValue, $isConvertIntoAssociativeArray = true)
    {
        return json_decode(base64_decode(strtr($encodedValue, '-_', '+/')), $isConvertIntoAssociativeArray);
    }

    public function equals(WpTesting_Model_AbstractModel $object)
    {
        if (is_null($object)) {
            return false;
        }
        return ($this->get('id') == $object->get('id'));
    }

    /**
     * @param WpTesting_WordPressFacade $wp
     * @throws InvalidArgumentException
     * @return self
     */
    public function setWp($wp)
    {
        if (!($wp instanceof WpTesting_WordPressFacade)) {
            throw new InvalidArgumentException('Wordpress facade is not those who you think it is.');
        }
        $this->wp = $wp;
        return $this;
    }

    /**
     * Generates phpdoc for class
     * @return string
     */
    public function reflectPhpDoc()
    {
        $signatures = array();

        $class        = get_class($this);
        $table        = fORM::tablize($class);
        $schema       = fORMSchema::retrieve($class);
        foreach ($schema->getColumnInfo($table) as $column => $columnInfo) {
            $camelizedColumn = fGrammar::camelize($column, true);

            // Get and set methods
            $fixedType = $columnInfo['type'];
            if ($fixedType == 'blob') {
                $fixedType = 'string';
            }
            if ($fixedType == 'varchar') {
                $fixedType = 'string';
            }
            if ($fixedType == 'date') {
                $fixedType = 'fDate|string';
            }
            if ($fixedType == 'timestamp') {
                $fixedType = 'fTimestamp|string';
            }
            if ($fixedType == 'time') {
                $fixedType = 'fTime|string';
            }
            $fixedTypes     = explode('|', $fixedType);
            $firstFixedType = reset($fixedTypes);

            $signatures[] = $this->generateMagicMethodPhpDoc(
                'get' . $camelizedColumn, array(), $firstFixedType, "Gets the current value of $column");
            $signatures[] = $this->generateMagicMethodPhpDoc(
                'set' . $camelizedColumn, array($fixedType => $column), $class, "Sets the value for $column");
        }

        return $signatures;
    }

    /**
     * Helps to cache method results
     * @see fActiveRecord::__call()
     */
    public function __call($methodName, $params)
    {
        // Call method only once
        if (strrpos($methodName, 'Once') !== false) {
            $methodName = str_replace('Once', '', $methodName);
            if (!isset($this->methodCallCache[$methodName])) {
                $this->methodCallCache[$methodName] = (method_exists($this, $methodName))
                    ? $this->$methodName($params)
                    : parent::__call($methodName, $params);
            }
            return $this->methodCallCache[$methodName];
        }
        return parent::__call($methodName, $params);
    }

    /**
     * @param string $relatedModelClassName
     * @param fRecordSet|array $records
     * @param string $route
     * @return self
     */
    protected function associateRelated($relatedModelClassName, $records, $route = null)
    {
        $this->__call('associate' . $relatedModelClassName, array($records, $route));
        return $this;
    }

    /**
     * @param string $relatedModelClassName
     * @param array $params
     * @return fRecordSet
     */
    protected function buildRelated($relatedModelClassName, $params = array())
    {
        return $this->__call('build' . $relatedModelClassName, $params);
    }

    /**
     * @param string $relatedModelClassName
     * @param string $route
     * @return WpTesting_Model_AbstractModel
     */
    protected function createRelated($relatedModelClassName, $route = null)
    {
        return $this->__call('create' . $relatedModelClassName, $route);
    }

    /**
     * @param string $relatedModelClassName
     * @param string $route
     * @return boolean
     */
    protected function hasRelated($relatedModelClassName, $route = null)
    {
        return (boolean)$this->__call('has' . $relatedModelClassName, $route);
    }

    /**
     * @param string $relatedModelClassName
     * @param string $route
     * @return self
     */
    protected function linkRelated($relatedModelClassName, $route = null)
    {
        $this->__call('link' . $relatedModelClassName, array($route));
        return $this;
    }

    /**
     * @param string $relatedModelClassName
     * @param string $route
     * @return array
     */
    protected function listRelated($relatedModelClassName, $route = null)
    {
        return $this->__call('list' . $relatedModelClassName, array($route));
    }

    /**
     * @param string $relatedModelClassName
     * @param boolean $isRecursive
     * @param string $route
     * @return self
     */
    protected function populateRelated($relatedModelClassName, $isRecursive = false, $route = null)
    {
        $this->__call('populate' . $relatedModelClassName, array($isRecursive, $route));
        return $this;
    }

    /**
     * @param string $methodName
     * @param array $params
     * @param string $returnType
     * @param string $comment
     * @return string
     */
    protected function generateMagicMethodPhpDoc($methodName, $params, $returnType, $comment)
    {
        $paramsDoc = array();
        foreach ($params as $type => $name) {
            $paramsDoc[] = "$type \$$name";
        }
        $paramsDoc  = implode(', ', $paramsDoc);
        $commentDoc = preg_replace('/\s+/', ' ', $comment);
        return " * @method $returnType $methodName($paramsDoc) $commentDoc";
    }

    protected function loadFromResult($result, $ignoreIdentityMap=false)
    {
        $row = $result->current();
        foreach ($row as $key => $value) {
            $row[$key] = $value;
        }
        return parent::loadFromResult(new ArrayIterator(array($row)), $ignoreIdentityMap);
    }

    /**
     * @see fActiveRecord::get()
     */
    protected function get($column)
    {
        return parent::get($this->deAliasColumn($column));
    }

    /**
     * @see fActiveRecord::set()
     */
    protected function set($column, $value)
    {
        return parent::set($this->deAliasColumn($column), $value);
    }

    /**
     * @param string $column
     * @return string
     */
    private function deAliasColumn($column)
    {
        return (isset($this->columnAliases[$column])) ? $this->columnAliases[$column] : $column;
    }

    /**
     * mb_strlen graceful degradation
     * @param string $string
     * @return int
     */
    protected function stringLength($string)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string);
        }
        if (function_exists('iconv_strlen')) {
            return iconv_strlen($string);
        }
        return strlen($string);
    }

    /**
     * @return WpTesting_WordPressFacade
     */
    protected function getWp()
    {
        if (is_null($this->wp)) {
            $this->setWp(new WpTesting_WordPressFacade('../../wp-testing.php'));
        }
        return $this->wp;
    }

    public function hasRelatedIn($records, $class)
    {
        foreach ($records as $record) {
            if (isset($record->related_records[fORM::tablize($class)])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Associate many records with it's related records by foreign key in one query
     * @param fRecordset|array $records
     * @param string $relatedClassName
     * @param string $foreignKeyName
     * @return array Objects fo type relatedClassName by it's ids
     */
    protected function associateManyRelated($records, $relatedClassName, $foreignKeyName)
    {
        $recordsById = array();
        if ($records instanceof fRecordSet) {
            foreach ($records as $record) {
                $recordsById[$record->getId()] = $record;
            }
        } else {
            $recordsById = $records;
        }

        if (empty($recordsById)) {
            return array();
        }

        // Get related records
        $orderBys = fORMRelated::getOrderBys(get_class(reset($recordsById)), $relatedClassName, $foreignKeyName);
        $relatedRecords = fRecordSet::build($relatedClassName, array(
            $foreignKeyName . '=' => array_keys($recordsById),
        ), $orderBys);
        $relatedRecordsById           = array();
        $relatedRecordsByByForeignKey = array();
        foreach ($relatedRecords as $relatedRecord) {
            $relatedRecordsById[$relatedRecord->getId()] = $relatedRecord;
            $relatedRecordsByByForeignKey[$relatedRecord->get($foreignKeyName)][] = $relatedRecord;
        }
        // Assoc related records to records
        $associateMethodName = 'associate' . $relatedClassName;
        foreach ($relatedRecordsByByForeignKey as $foreignKeyValue => $relatedRecords) {
            $recordsById[$foreignKeyValue]->$associateMethodName($relatedRecords);
        }
        return $relatedRecordsById;
    }

    protected function transactionStart()
    {
        $db = $this->getDb();
        if (!$db->isInsideTransaction()) {
            $db->translatedQuery('BEGIN');
            $this->isTransactionStarted = true;
        }
        return $this;
    }

    protected function transactionFinish()
    {
        if ($this->isTransactionStarted) {
            $this->getDb()->translatedQuery('COMMIT');
            $this->isTransactionStarted = false;
        }
        return $this;
    }

    protected function transactionRollback()
    {
        $db = $this->getDb();
        if (!$db->isInsideTransaction()) {
            $db->translatedQuery('ROLLBACK');
        }
        return $this;
    }

    protected function getDb($role = 'write')
    {
        return fORMDatabase::retrieve(get_class($this), $role);
    }

    /**
     * Strip slashes from values.
     * It's an antipod of wp_magic_quotes.
     * @return WpTesting_Model_AbstractModel
     */
    private function stripValuesSlashes()
    {
        foreach ($this->values as $key => $value) {
            if (is_string($value) && strstr($value, '\\') !== false) {
                $this->values[$key] = stripslashes($value);
            }
        }

        return $this;
    }

}