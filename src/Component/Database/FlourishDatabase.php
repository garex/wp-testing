<?php

class WpTesting_Component_Database_FlourishDatabase extends fDatabase
{
    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $host = new WpTesting_WordPress_Value_DbHost($wp->getDbHost());

        parent::__construct(
            'mysql',
            $wp->getDbName(),
            $wp->getDbUser(),
            $wp->getDbPassword(),
            $host->socket() ? 'sock:' . $host->socket() : $host->host(),
            $host->port(),
            null,
            $wp->getDbCharset()
        );
    }
}
