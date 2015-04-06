<?php
class WpTesting_Model_Step
{

    private $title = '';
    private $questions = array();

    private $total = 1;
    private $number = 1;

    public function __construct($title, fRecordSet $questions)
    {
        $this->title     = $title;
        $this->questions = $questions;
    }

    public function setTotalAndNumber($total, $number)
    {
        if ($number < 1 || $number > $total) {
            throw new InvalidArgumentException("Invalid step number provided: '$number' out of '$total'");
        }
        $this->total  = intval($total);
        $this->number = intval($number);
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return WpTesting_Model_Question[]
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return boolean
     */
    public function isFirst()
    {
        return ($this->number == 1);
    }

    /**
     * @return boolean
     */
    public function isLast()
    {
        return ($this->number == $this->total);
    }
}
