<?php

class WpTesting_Migration_AddSectionDescription extends WpTesting_Migration_AddColumn
{

    protected $columns = array(
        array(
            'table'   => 'sections',
            'column'  => 'section_description',
            'type'    => 'mediumtext',
        ),
    );
}
