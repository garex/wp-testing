<?php

abstract class BaseMigration extends Ruckusing_Migration_Base
{

    /**
     * Get default wordpress tables engine
     * @return string
     */
    protected function get_wp_table_engine()
    {
        $engine = $this->field('
            SELECT ENGINE FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "' . WP_DB_PREFIX . 'posts"
        ');
        if (empty($engine)) {
            $engine = 'InnoDB';
        }
        return $engine;
    }

    /**
     * Select first field value
     *
     * @param string $sql the query to run
     *
     * @return string
     */
    protected function field($sql)
    {
        $result = $this->select_one($sql);
        if (empty($result)) {
            return null;
        }
        return reset($result);
    }
}
