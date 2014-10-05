<?php

class WpTesting_Model_FormulaVariable
{

    private $model = null;

    public function __construct(WpTesting_Model_AbstractModel $model)
    {
        $this->model = $model;
    }

    public function getValue()
    {
        $model = $this->model;
        if ($model instanceof WpTesting_Model_AbstractTerm) {
            return $model->getTitle();
        }
        return null;
    }

    public function getTitle()
    {
        $model = $this->model;
        if ($model instanceof WpTesting_Model_Scale) {
            return 'Scale Variable';
        }
        return 'Variable';
    }

    public function getSource()
    {
        $model = $this->model;
        if ($model instanceof WpTesting_Model_AbstractTerm) {
            return $model->getSlug();
        }
        return null;
    }
}
