<?php

class WpTesting_Model_Scale extends WpTesting_Model_AbstractTerm
{

    protected $scoreTotal = 0;

    /**
     * Scale for total values
     * @var WpTesting_Model_Scale
     */
    protected $totalScale = null;

    public function resetScore()
    {
        $this->scoreTotal = 0;
        return $this;
    }

    public function addScore(WpTesting_Model_Score $score)
    {
        $this->scoreTotal += $score->getValue();
        return $this;
    }

    public function getScoresTotal()
    {
        return $this->scoreTotal;
    }

    public function getScoresTotalPercent()
    {
        $totalTotal = $this->getTotalScale()->getScoresTotal();
        if (!$totalTotal) {
            return 0;
        }
        return round($this->scoreTotal / $totalTotal * 100);
    }

    public function setTotalScale(WpTesting_Model_Scale $scale)
    {
        $this->totalScale = $scale;
    }

    public function getTotalScale()
    {
        if ($this->totalScale instanceof WpTesting_Model_Scale) {
            return $this->totalScale;
        }
        return new WpTesting_Model_Scale();
    }

}
