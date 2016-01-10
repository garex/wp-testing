<?php

/**
 * Knows about WordPress pathes and directories
 */
interface WpTesting_WordPress_IPath
{

    /**
     * Absolute path to the WordPress directory.
     * @return string
     */
    public function getAbsPath();

    /**
     * Allows for the plugins directory to be moved from the default location.
     *
     * @since 2.6.0
     * @return string
     */
    public function getPluginDir();

    /**
     * Determines a writable directory for temporary files (with trailing slahs added).
     *
     * @since 2.5.0
     *
     * @return string Writable temporary directory
     */
    public function getTempDir();

    /**
     * Where WordPress holds it's public content
     *
     * @return string
     */
    public function getContentDir();

    /**
     * Appends a trailing slash.
     *
     * Will remove trailing forward and backslashes if it exists already before adding
     * a trailing forward slash. This prevents double slashing a string or path.
     *
     * The primary use of this is for paths and thus should be used for paths. It is
     * not restricted to paths and offers no specific path support.
     *
     * @since 1.2.0
     *
     * @param string $path What to add the trailing slash to.
     * @return string String with trailing slash added.
     */
    public function appendSlash($path);
}
