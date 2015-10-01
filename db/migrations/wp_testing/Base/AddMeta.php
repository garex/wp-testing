<?php

abstract class WpTesting_Migration_AddMeta extends WpTesting_Migration_Base
{

    /**
     * Name => default value
     * @var array
     */
    protected $metas = array();

    public function up()
    {
        foreach ($this->metas as $key => $value) {
            $this->addMeta($key, $value);
        }
    }

    public function down()
    {
        foreach (array_keys($this->metas) as $key) {
            $this->removeMeta($key);
        }
    }

    protected function addMeta($key, $value)
    {
        $this->execute("
            INSERT INTO {$this->globalPrefix}postmeta(post_id, meta_key, meta_value)
            SELECT ID, '$key', '$value'
            FROM {$this->globalPrefix}posts WHERE post_type = 'wpt_test'
        ");
    }

    protected function removeMeta($key)
    {
        $this->execute("
            DELETE FROM {$this->globalPrefix}postmeta
            WHERE meta_key = '$key'
        ");
    }
}
