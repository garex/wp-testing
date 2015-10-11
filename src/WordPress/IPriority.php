<?php

/**
 * Used to specify the order in which the functions associated with a particular action are executed (default: 10).
 * Lower numbers correspond with earlier execution, and functions with the same priority
 * are executed in the order in which they were added to the action.
 */
interface WpTesting_WordPress_IPriority
{

    /**
     * @var integer
     */
    const PRIORITY_HIGH = 5;

    /**
     * @var integer
     */
    const PRIORITY_MEDIUM = 8;

    /**
     * @var integer
     */
    const PRIORITY_DEFAULT = 10;
}
