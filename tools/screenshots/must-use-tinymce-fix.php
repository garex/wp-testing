<?php
add_action('before_wp_tiny_mce', function () {
    echo '<script>if (!window.URL) {var URL = {};}</script>';
});
