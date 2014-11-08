<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

/**
 * Required mostly for quick unit testing
 */
class AddWpTables extends BaseMigration
{
    public function up()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS ' . WP_DB_PREFIX . 'posts (
              ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              post_author bigint(20) unsigned NOT NULL DEFAULT "0",
              post_date datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
              post_date_gmt datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
              post_content longtext NOT NULL,
              post_title text NOT NULL,
              post_excerpt text NOT NULL,
              post_status varchar(20) NOT NULL DEFAULT "publish",
              comment_status varchar(20) NOT NULL DEFAULT "open",
              ping_status varchar(20) NOT NULL DEFAULT "open",
              post_password varchar(20) NOT NULL DEFAULT "",
              post_name varchar(200) NOT NULL DEFAULT "",
              to_ping text NOT NULL,
              pinged text NOT NULL,
              post_modified datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
              post_modified_gmt datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
              post_content_filtered longtext NOT NULL,
              post_parent bigint(20) unsigned NOT NULL DEFAULT "0",
              guid varchar(255) NOT NULL DEFAULT "",
              menu_order int(11) NOT NULL DEFAULT "0",
              post_type varchar(20) NOT NULL DEFAULT "post",
              post_mime_type varchar(100) NOT NULL DEFAULT "",
              comment_count bigint(20) NOT NULL DEFAULT "0",
              PRIMARY KEY (ID),
              KEY post_name (post_name),
              KEY type_status_date (post_type,post_status,post_date,ID),
              KEY post_parent (post_parent),
              KEY post_author (post_author)
            ) DEFAULT CHARSET=utf8
        ');
        $this->execute('
            CREATE TABLE IF NOT EXISTS ' . WP_DB_PREFIX . 'terms (
              term_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              name varchar(200) NOT NULL DEFAULT "",
              slug varchar(200) NOT NULL DEFAULT "",
              term_group bigint(10) NOT NULL DEFAULT "0",
              PRIMARY KEY (term_id),
              UNIQUE KEY slug (slug),
              KEY name (name)
            ) DEFAULT CHARSET=utf8
        ');
        $this->execute('
            CREATE TABLE IF NOT EXISTS ' . WP_DB_PREFIX . 'users (
              ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              user_login varchar(60) NOT NULL DEFAULT "",
              user_pass varchar(64) NOT NULL DEFAULT "",
              user_nicename varchar(50) NOT NULL DEFAULT "",
              user_email varchar(100) NOT NULL DEFAULT "",
              user_url varchar(100) NOT NULL DEFAULT "",
              user_registered datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
              user_activation_key varchar(60) NOT NULL DEFAULT "",
              user_status int(11) NOT NULL DEFAULT "0",
              display_name varchar(250) NOT NULL DEFAULT "",
              PRIMARY KEY (ID),
              KEY user_login_key (user_login),
              KEY user_nicename (user_nicename)
            ) DEFAULT CHARSET=utf8
        ');

        $this->execute('
            CREATE TABLE IF NOT EXISTS ' . WP_DB_PREFIX . 'term_taxonomy (
              term_taxonomy_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              term_id bigint(20) unsigned NOT NULL DEFAULT "0",
              taxonomy varchar(32) NOT NULL DEFAULT "",
              description longtext NOT NULL,
              parent bigint(20) unsigned NOT NULL DEFAULT "0",
              count bigint(20) NOT NULL DEFAULT "0",
              PRIMARY KEY (term_taxonomy_id),
              UNIQUE KEY term_id_taxonomy (term_id,taxonomy),
              KEY taxonomy (taxonomy)
            ) DEFAULT CHARSET=utf8
        ');
        $this->execute('
            CREATE TABLE IF NOT EXISTS ' . WP_DB_PREFIX . 'term_relationships (
              object_id bigint(20) unsigned NOT NULL DEFAULT "0",
              term_taxonomy_id bigint(20) unsigned NOT NULL DEFAULT "0",
              term_order int(11) NOT NULL DEFAULT "0",
              PRIMARY KEY (object_id,term_taxonomy_id),
              KEY term_taxonomy_id (term_taxonomy_id)
            ) DEFAULT CHARSET=utf8
        ');
        $this->execute('
            CREATE TABLE IF NOT EXISTS ' . WP_DB_PREFIX . 'postmeta (
                meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                post_id bigint(20) unsigned NOT NULL DEFAULT "0",
                meta_key varchar(255) DEFAULT NULL,
                meta_value longtext,
                PRIMARY KEY (meta_id),
                KEY post_id (post_id),
                KEY meta_key (meta_key)
            ) DEFAULT CHARSET=utf8
        ');
    }

    public function down()
    {
        // do nothing
    }
}
