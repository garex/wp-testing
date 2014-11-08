<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddPublishOnHomeMeta extends BaseMigration
{
    public function up()
    {
        $meta    = WP_DB_PREFIX  . 'postmeta';
        $posts   = WP_DB_PREFIX  . 'posts';
        $this->execute("
            INSERT INTO $meta(post_id, meta_key, meta_value)
            SELECT ID, 'wpt_publish_on_home', 1
            FROM $posts WHERE post_type = 'wpt_test'
        ");
    }

    public function down()
    {
        $meta    = WP_DB_PREFIX  . 'postmeta';
        $this->execute("
            DELETE FROM $meta
            WHERE meta_key = 'wpt_publish_on_home'
        ");
    }
}
