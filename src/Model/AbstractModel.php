<?php
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

    public function populate($recursive = false)
    {
        parent::populate($recursive);

        return $this->stripValuesSlashes();
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
            $camelizedColumn = fGrammar::camelize($column, TRUE);

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
            $firstFixedType = reset(explode('|', $fixedType));

            $signatures[] = $this->generateMagicMethodPhpDoc(
                'get' . $camelizedColumn, array(), $firstFixedType, "Gets the current value of $column");
            $signatures[] = $this->generateMagicMethodPhpDoc(
                'set' . $camelizedColumn, array($fixedType => $column), $class, "Sets the value for $column");
        }

        return $signatures;
    }

    protected function generateMagicMethodPhpDoc($methodName, $params, $returnType, $comment)
    {
        $paramsDoc = array();
        foreach ($params as $type => $name) {
            $paramsDoc[] = "$type \$$name";
        }
        $paramsDoc  = implode(', ', $paramsDoc);
        $commentDoc = preg_replace('/\s+/', ' ', $comment);
        return " * @method $returnType $methodName() $methodName($paramsDoc) $commentDoc";
    }

    protected function loadFromResult($result, $ignore_identity_map=FALSE)
    {
        $row = $result->current();
        foreach ($row as $key => $value) {
            $row[$key] = $value;
        }
        return parent::loadFromResult(new ArrayIterator(array($row)), $ignore_identity_map);
    }

    /**
     * @see fActiveRecord::get()
     */
    protected function get($column)
    {
        if (isset($this->columnAliases[$column])) {
            $column = $this->columnAliases[$column];
        }
        return parent::get($column);
    }

    /**
     * @see fActiveRecord::set()
     */
    protected function set($column, $value)
    {
        if (isset($this->columnAliases[$column])) {
            $column = $this->columnAliases[$column];
        }
        return parent::set($column, $value);
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

    protected function getWp()
    {
        if (is_null($this->wp)) {
            $this->setWp(new WpTesting_WordPressFacade('../../wp-testing.php'));
        }
        return $this->wp;
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