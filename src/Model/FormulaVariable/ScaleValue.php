<?php

class WpTesting_Model_FormulaVariable_ScaleValue extends WpTesting_Model_FormulaVariable implements WpTesting_Model_FormulaVariable_IAllBuilder
{

    /** @var WpTesting_Model_Scale */
    private $scale;

    public static function buildAllFrom(WpTesting_Model_Test $test, WpTesting_Model_Passing $passing = null)
    {
        $variables = array();

        if (is_null($passing)) {
            $scalesWithRange = $test->buildScalesWithRange();
        } else {
            $scalesWithRange = $passing->buildScalesWithRangeOnce();
        }
        $typeLabel = __('Scale Variable', 'wp-testing');
        foreach ($scalesWithRange as $scale) {
            $variable = new self();
            $variable->scale = $scale;
            $variable
                ->setTypeLabel($typeLabel)
                ->setSource(urldecode($scale->getSlug()))
                ->setValue($scale->getValue())
                ->setValueAsRatio($scale->getValueAsRatio())
            ;
            $variables[$variable->getSource()] = $variable;
        }

        return $variables;
    }

    public function getTitle()
    {
        return $this->scale->getTitle() . ', ' . $this->scale->getAggregatesTitle();
    }
}