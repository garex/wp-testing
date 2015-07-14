<?php

abstract class WpTesting_Model_FormulaVariable
{

    private $title        = '';
    private $typeLabel    = '';
    private $source       = '';
    private $value        = 0;
    private $valueAsRatio = 0;

    public function getTitle()
    {
        return $this->title;
    }

    public function getTypeLabel()
    {
        return $this->typeLabel;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueAsRatio()
    {
        return $this->valueAsRatio;
    }

    protected function setTitle($value)
    {
        $this->title = $value;
        return $this;
    }

    protected function setTypeLabel($value)
    {
        $this->typeLabel = $value;
        return $this;
    }

    protected function setSource($value)
    {
        $this->source = $value;
        return $this;
    }

    protected function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    protected function setValueAsRatio($value)
    {
        $this->valueAsRatio = $value;
        return $this;
    }
}
