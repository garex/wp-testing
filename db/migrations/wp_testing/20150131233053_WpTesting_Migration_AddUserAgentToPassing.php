<?php

class WpTesting_Migration_AddUserAgentToPassing extends WpTesting_Migration_AddColumn
{

    protected $columns = array(
        array(
            'table'   => 'passings',
            'column'  => 'user_agent',
            'type'    => 'text',
        ),
    );
}
