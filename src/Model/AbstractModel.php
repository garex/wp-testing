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
            $row[strtolower($key)] = $value;
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
}