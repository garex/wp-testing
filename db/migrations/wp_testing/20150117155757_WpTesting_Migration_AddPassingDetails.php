<?php

class WpTesting_Migration_AddPassingDetails extends WpTesting_Migration_AddColumn
{

    protected $columns = array(
        array(
            'table'   => 'passings',
            'column'  => 'ip',
            'type'    => 'string',
            'options' => array('limit' => 45),
        ),
        array(
            'table'   => 'passings',
            'column'  => 'device_uuid',
            'type'    => 'uuid',
        ),
    );
}
