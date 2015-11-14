<?php

class WpTesting_Migration_AddStatusToPassings extends WpTesting_Migration_AddColumn
{

    protected $columns = array(
        array(
            'table'   => 'passings',
            'column'  => 'passing_status',
            'type'    => 'enum',
            'options' => array(
                'after'   => 'respondent_id',
                'values'  => array('publish', 'trash'),
                'default' => 'publish',
                'null'    => false,
            )
        ),
    );

    private $indexColumns = array('passing_status', 'passing_created', 'passing_id');
    private $indexOptions = array('name' => 'i_passing_status_created_id');

    public function up()
    {
        parent::up();
        $this->addIndex('passings', $this->indexColumns, $this->indexOptions);
    }

    public function down()
    {
        $this->removeIndex('passings', $this->indexColumns, $this->indexOptions);
        parent::down();
    }
}
