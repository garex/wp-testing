<?php

/**
 * Exclusively filters main content and could be activated and deactivated.
 *
 * In opened state it could accept new requests and process it.
 * In closed state it will not accept new requests but able to process existing requests.
 */
interface WpTesting_Doer_IExclusiveContentFilterer
{

    /**
     * Filterer will accept new requests and process it.
     *
     * @return self
     */
    public function open();

    /**
     * Filterer will not accept new requests but will process existing.
     *
     * @return self
     */
    public function close();
}
