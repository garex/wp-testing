<?php

/**
 * @method integer getId() getId() Gets the current value of id
 * @method string getTitle() getTitle() Gets the current value of title
 */
class WpTesting_Model_Question extends WpTesting_Model_AbstractModel
{

    protected $columnAliases = array(
        'title'  => 'question_title',
        'id'     => 'question_id',
    );

}
