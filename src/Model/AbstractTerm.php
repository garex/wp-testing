<?php

/**
 * @method string getTitle() getTitle() Gets the current value of title
 */
abstract class WpTesting_Model_AbstractTerm extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'title' => 'name',
    );

    /**
     * Abbreviration of title
     *
     * @return string
     */
    public function getAbbr()
    {
        return mb_substr($this->getTitle(), 0, 1, 'UTF-8');
    }

}
