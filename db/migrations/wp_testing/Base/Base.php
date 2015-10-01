<?php

abstract class WpTesting_Migration_Base
{

    protected $globalPrefix = '';

    protected $pluginPrefix = '';

    /**
     * @var Ruckusing_Migration_Base
     */
    protected $adaptee;

    /**
     * @param Ruckusing_Adapter_Base $dbAdapter
     */
    public function __construct($dbAdapter)
    {
        $this->adaptee = new Ruckusing_Migration_Base($dbAdapter);
        $this->setUp();
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->globalPrefix = WP_DB_PREFIX;
        $this->pluginPrefix = WPT_DB_PREFIX;
    }

    /**
     * @return void
     */
    abstract public function up();

    /**
     * @return void
     */
    abstract public function down();

    public function execute($query)
    {
        return $this->adaptee->execute($query);
    }
}
