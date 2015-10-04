<?php

class WpTesting_Migration_AddSortToAnswers extends WpTesting_Migration_AddColumn
{

    protected $columns = array(
        array(
            'table'   => 'answers',
            'column'  => 'answer_sort',
            'type'    => 'integer',
            'options' => array('default' => 100)
        ),
    );
}
