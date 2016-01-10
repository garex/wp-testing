<?php

/**
 * Reads and writes post's meta
 */
interface WpTesting_WordPress_IPostMeta
{

    /**
     * Retrieve post meta field for a post.
     *
     * @since 1.5.0
     * @uses $wpdb
     * @link http://codex.wordpress.org/Function_Reference/get_post_meta
     *
     * @param string $key The meta key to retrieve.
     * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
     *  is true.
     */
    public function getCurrentPostMeta($key);

    /**
     * Retrieve post meta field for a post.
     *
     * @since 1.5.0
     * @uses $wpdb
     * @link http://codex.wordpress.org/Function_Reference/get_post_meta
     *
     * @param int $postId Post ID.
     * @param string $key The meta key to retrieve.
     * @param bool $isSingle Whether to return a single value.
     * @return mixed Will be an array if $single is false.
     *               Will be value of meta data field if $single is true.
     */
    public function getPostMeta($postId, $key, $isSingle);

    /**
     * Update post meta field based on post ID.
     *
     * Use the $prev_value parameter to differentiate between meta fields with the
     * same key and post ID.
     *
     * If the meta field for the post does not exist, it will be added.
     *
     * @since 1.5.0
     * @uses $wpdb
     * @link http://codex.wordpress.org/Function_Reference/update_post_meta
     *
     * @param int $postId Post ID.
     * @param string $key Metadata key.
     * @param mixed $value Metadata value.
     * @param mixed $previousValue Optional. Previous value to check before removing.
     * @return bool False on failure, true if success.
     */
    public function updatePostMeta($postId, $key, $value, $previousValue = '');
}
