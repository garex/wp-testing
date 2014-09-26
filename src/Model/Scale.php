<?php

class WpTesting_Model_Scale extends WpTesting_Model_AbstractTerm
{

    protected $scoreTotal = 0;

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

}
