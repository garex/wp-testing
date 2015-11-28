<?php

abstract class WpTesting_Migration_Base
{

    protected $globalPrefix = '';

    protected $blogPrefix = '';

    protected $pluginPrefix = '';

    /**
     * @var Ruckusing_Migration_Base
     */
    protected $adaptee;

    /**
     * @param Ruckusing_Adapter_Base $dbAdapter
     */
    public function __construct(Ruckusing_Adapter_Base $dbAdapter)
    {
        $this->adaptee = new Ruckusing_Migration_Base($dbAdapter);
        $this->setUp($dbAdapter->get_dsn());
    }

    /**
     * @param array $dsn
     * @return void
     */
    protected function setUp($dsn)
    {
        $this->globalPrefix = $dsn['globalPrefix'];
        $this->blogPrefix   = $dsn['blogPrefix'];
        $this->pluginPrefix = $dsn['pluginPrefix'];
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
