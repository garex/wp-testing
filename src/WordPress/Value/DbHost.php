<?php

/**
 * Parse database host/port/socket from DB_HOST format.
 */
class WpTesting_WordPress_Value_DbHost extends WpTesting_WordPress_Value_Base
{
    private $host;
    private $port;
    private $socket;

    public function __construct($value)
    {
        parent::__construct($value);

        // First peel off the socket parameter from the right, if it exists.
        $socketPosition = strpos($value, ':/' );
        if ($socketPosition !== false) {
            $this->socket = substr($value, $socketPosition + 1);
            $value = substr($value, 0, $socketPosition);
        }

        // We need to check for an IPv6 address first.
        // An IPv6 address will always contain at least two colons.
        $isIpv6 = (substr_count($value, ':') > 1);

        if ($isIpv6) {
            $pattern = '#^(?:\[)?(?<host>[0-9a-fA-F:]+)(?:\]:(?<port>[\d]+))?#';
        } else {
            $pattern = '#^(?<host>[^:/]*)(?::(?<port>[\d]+))?#';
        }

        $matches = array();
        $result = preg_match($pattern, $value, $matches);

        if (!empty($matches['host'])) {
            $this->host = $matches['host'];
            if ($isIpv6 && extension_loaded('mysqlnd')) {
                $this->host = '['.$this->host.']';
            }
        }
        if (!empty($matches['port'])) {
            $this->port = $matches['port'];
        }
    }

    /**
     * @return string
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function port()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function socket()
    {
        return $this->socket;
    }
}
