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
        $this->adaptee->get_adapter()->logger->log(trim($query, ';') . ';');

        return $this->adaptee->execute($query);
    }

    /**
     * Safely execute query ignoring fails
     *
     * @param string $query
     *
     * @return boolean
     */
    protected function executeSafely($query)
    {
        $result = true;
        foreach (explode(';', $query) as $singleQuery) {
            if (!trim($singleQuery)) {
                continue;
            }
            try {
                $result = $this->execute($singleQuery);
            } catch (Ruckusing_Exception $e) {
                $this->adaptee->get_adapter()->logger->log(__METHOD__ . ': ' . $e->getMessage());
                $result = false;
            }
        }
        return $result;
    }
}
