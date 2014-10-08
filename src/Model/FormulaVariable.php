<?php

class WpTesting_Model_FormulaVariable
{

    private $model = null;

    public function __construct(WpTesting_Model_AbstractModel $model)
    {
        $this->model = $model;
    }

    public function getTitle()
    {
        $model = $this->model;
        if ($model instanceof WpTesting_Model_AbstractTerm) {
            return $model->getTitle() . ', ∑ ' . $model->getMaximum();
        }
        return null;
    }

    public function getTypeLabel()
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

    public function getValue()
    {
        $model = $this->model;
        if ($model instanceof WpTesting_Model_Scale) {
            return $model->getValue();
        }
        return 0;
    }

    public function getValueAsRatio()
    {
        $model = $this->model;
        if ($model instanceof WpTesting_Model_Scale) {
            return $model->getValueAsRatio();
        }
        return 0;
    }
}
