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

    protected function add_meta($key, $value)
    {
        $meta    = WP_DB_PREFIX  . 'postmeta';
        $posts   = WP_DB_PREFIX  . 'posts';
        $this->execute("
            INSERT INTO $meta(post_id, meta_key, meta_value)
            SELECT ID, '$key', '$value'
            FROM $posts WHERE post_type = 'wpt_test'
        ");
    }

    protected function remove_meta($key)
    {
        $meta    = WP_DB_PREFIX  . 'postmeta';
        $this->execute("
            DELETE FROM $meta
            WHERE meta_key = '$key'
        ");
    }

    protected function update_meta_in_example($key, $value)
    {
        $meta  = WP_DB_PREFIX  . 'postmeta';
        $posts = WP_DB_PREFIX  . 'posts';
        $this->execute("
            UPDATE $posts AS p, $meta AS m
            SET m.meta_value = '$value'
            WHERE TRUE
            AND m.meta_key   = '$key'
            AND p.post_type  = 'wpt_test'
            AND p.post_title = 'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'
            AND m.post_id    = p.ID
        ");
    }
}
