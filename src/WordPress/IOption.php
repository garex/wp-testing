<?php

/**
 * Reads and writes global options
 */
interface WpTesting_WordPress_IOption
{

    /**
     * Retrieve option value based on name of option.
     *
     * If the option does not exist or does not have a value, then the return value
     * will be false. This is useful to check whether you need to install an option
     * and is commonly used during installation of plugin options and to test
     * whether upgrading is required.
     *
     * If the option was serialized then it will be unserialized when it is returned.
     *
     * @since 1.5.0
     *
     * @param string $option  Name of option to retrieve. Expected to not be SQL-escaped.
     * @param mixed  $default Optional. Default value to return if the option does not exist.
     * @return mixed Value set for the option.
     */
    public function getOption($option, $default = false);

    /**
     * Update the value of an option that was already added.
     *
     * You do not need to serialize values. If the value needs to be serialized, then
     * it will be serialized before it is inserted into the database. Remember,
     * resources can not be serialized or added as an option.
     *
     * If the option does not exist, then the option will be added with the option value
     *
     * @since 1.0.0
     *
     * @param string      $option   Option name. Expected to not be SQL-escaped.
     * @param mixed       $value    Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
     * @return bool False if value was not updated and true if value was updated.
     */
    public function updateOption($option, $value);
}
