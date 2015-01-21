<?php

abstract class WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->wp = $wp;
        $this->registerScripts();
    }

    /**
     * Register common used scripts for future dependencies
     */
    protected function registerScripts()
    {
        $this->wp
            ->registerPluginScript('lodash-source', 'js/vendor/lodash/lodash.compat.min.js', array(), '2.4.1')
            ->registerPluginScript('lodash', 'js/vendor/lodash/lodash.no-conflict.js', array('lodash-source'))
            ->registerPluginScript('npm-stub', 'js/vendor/npm/stub.js', array(), '1.0')
        ;
    }

    protected function output($__template, $__params = array())
    {
        if (substr($__template, -4) != '.php') {
            $__template = dirname(dirname(__FILE__)) . '/Template/' . $__template . '.php';
        }
        extract($__params, EXTR_SKIP);
        include $__template;
    }

    protected function render($__template, $__params = array())
    {
        ob_start();
        $this->output($__template, $__params);
        return ob_get_clean();
    }

    protected function isPost()
    {
        return fRequest::isPost();
    }

    protected function getRequestValue($key)
    {
        return fRequest::get($key);
    }

    /**
     * Checks if current WordPress version greater than or equal provided version
     *
     * @param string $version
     * @return boolean
     */
    protected function isWordPressAlready($version)
    {
        return version_compare($this->wp->getVersion(), $version, '>=');
    }

    /**
     * Get the IP the client is using, or says that using.
     *
     * @see http://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php/2031935#2031935
     *
     * @return string|null
     */
    protected function getClientIp() {
        $candidateKeys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',   'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );

        foreach ($candidateKeys as $key){
            $value = $this->getEnv($key);
            if ($key == 'REMOTE_ADDR' && $value == $this->getEnv('SERVER_ADDR')) {
                $value = $this->getEnv('HTTP_PC_REMOTE_ADDR');
            }

            if (empty($value)) {
                continue;
            }

            foreach (explode(',', $value) as $ip) {
                $ip = filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                if ($ip === false) {
                    continue;
                }
                return $ip;
            }
        }

        return null;
    }

    /**
     * Move source item before destination item in array
     *
     * @param array $input
     * @param string $sourceKey
     * @param string $destinationKey
     * @return array
     */
    protected function arrayMoveItemBefore($input, $sourceKey, $destinationKey)
    {
        return $this->arrayMoveItemTo($input, $sourceKey, $destinationKey, 'before');
    }

    /**
     * Move source item after destination item in array
     *
     * @param array $input
     * @param string $sourceKey
     * @param string $destinationKey
     * @return array
     */
    protected function arrayMoveItemAfter($input, $sourceKey, $destinationKey)
    {
        return $this->arrayMoveItemTo($input, $sourceKey, $destinationKey, 'after');
    }

    /**
     * @param array $input
     * @param string $sourceKey
     * @param string $destinationKey
     * @param string $placement before or after
     * @return array
     */
    private function arrayMoveItemTo($input, $sourceKey, $destinationKey, $placement)
    {
        if (!isset($input[$sourceKey]) || !isset($input[$destinationKey])) {
            return $input;
        }

        $sourceItem = $input[$sourceKey];
        unset($input[$sourceKey]);
        $result = array();

        foreach ($input as $key => $value) {
            if ('before' == $placement && $key == $destinationKey) {
                $result[$sourceKey] = $sourceItem;
            }
            $result[$key] = $value;
            if ('after' == $placement && $key == $destinationKey) {
                $result[$sourceKey] = $sourceItem;
            }
        }

        return $result;
    }

    /**
     * Gets an environment variable from available sources
     *
     * @see CakePHP's env function
     *
     * @param string $key Environment variable name.
     * @return string|null Environment variable setting.
     */
    private function getEnv($key)
    {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            return $_ENV[$key];
        } elseif (getenv($key) !== false) {
            return getenv($key);
        }
        return null;
    }

}
