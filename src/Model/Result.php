<?php

class WpTesting_Model_Result extends WpTesting_Model_AbstractTerm
{
    /**
     * @var WpTesting_Model_Test
     */
    private $test = null;

    public function setTest(WpTesting_Model_Test $test)
    {
        $this->test = $test;
        return $this;
    }

    /**
     * @return WpTesting_Model_Formula
     */
    public function getFormula()
    {
        $empty = new WpTesting_Model_Formula();
        $empty->setResultId($this->getId());
        if (is_null($this->test)) {
            return $empty;
        }
        $empty->setTestId($this->test->getId());

        /* @var $formulas fRecordSet */
        $formulas = $this->test->buildFormulasOnce();
        foreach ($formulas->filter(array('getResultId=' => $this->getId())) as $formula) {
            return $formula;
        }

        return $empty;
    }
}
