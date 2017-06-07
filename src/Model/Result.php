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
        $empty->setResultId($this->getIdOnce());
        if (is_null($this->test)) {
            return $empty;
        }
        $empty->setTestId($this->test->getIdOnce());

        $formulas = $this->test->buildFormulasOnce();
        foreach ($formulas as $formula) {
            if ($formula->getResultIdOnce() == $this->getIdOnce()) {
                return $formula;
            }
        }

        return $empty;
    }

    public function jsonSerialize()
    {
        return parent::jsonSerialize() + array(
            'editLink' => $this->getWp()->getEditTermLink($this->getId(), 'wpt_result', 'wpt_test'),
            'tooltip'  => $this->getDescriptionAsTooltip(),
            'formula'  => $this->getFormula(),
        );
    }
}
