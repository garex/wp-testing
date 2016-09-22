<?php

/**
 * Redirects to other URLs
 */
interface WpTesting_Doer_IRedirector
{

    /**
     * Redirects to link, safely finalizing Wordpress execution.
     *
     * Tries to redirect by headers, javascript and HTML metas finally.
     *
     * @return self
     */
    public function redirectAnyway($link);
}
