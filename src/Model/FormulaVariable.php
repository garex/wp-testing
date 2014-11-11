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
            return sprintf(__('%1$s, ∑ %2$d', 'wp-testing'), $model->getTitle(), $model->getSum());
        }
        return null;
    }

    public function getTypeLabel()
    {
        $model = $this->model;
        if ($model instanceof WpTesting_Model_Scale) {
            return __('Scale Variable', 'wp-testing');
        }
        return __('Variable', 'wp-testing');
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
