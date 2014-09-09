<?php
abstract class WpTesting_Model_AbstractModel extends fActiveRecord
{

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

}