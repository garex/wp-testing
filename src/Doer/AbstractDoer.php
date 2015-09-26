<?php

abstract class WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_WordPressFacade
     */
    protected $wp = null;

    /**
     * Data passed to javascript in a global Wpt object
     * @var array
     */
    private $jsData = array();

    private $resourceIdPrefix = null;

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        $this->wp           = $wp;
        $this->templateRoot = dirname(dirname($this->getClassFile())) . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR;
        $this->resourceIdPrefix = $this->getResourcePrefix('WpTesting', 'wpt_');
    }

    protected function getClassFile()
    {
        return __FILE__;
    }

    public function renderJsData()
    {
        $root = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR;
        $this->output($root . 'Abstract/js-data.php', array(
            'Wpt' => $this->jsData,
        ));
        $this->jsData = array();
    }

    /**
     * Creates test with injected WP facade
     * @param mixed $key
     * @return WpTesting_Model_Test
     */
    protected function createTest($key = null)
    {
        $test = new WpTesting_Model_Test($key);
        return $test->setWp($this->wp);
    }

    /**
     * Adds data to Wpt global object
     * @param string $key
     * @param mixed $value
     * @return self
     */
    protected function addJsData($key, $value)
    {
        if (empty($this->jsData)) {
            $actionTag = (!$this->wp->didAction('wp_print_scripts')) ? 'wp_print_scripts' : 'wp_print_footer_scripts';
            $this->wp->addAction($actionTag, array($this, 'renderJsData'));
        }
        $this->jsData[$key] = $value;
        return $this;
    }

    /**
     * Adds multiple data values to Wpt global object from values array
     * @see addJsData
     * @param array $values [key1 => value1, keyN => valueN]
     * @return self
     */
    protected function addJsDataValues($values)
    {
        foreach ($values as $key => $value) {
            $this->addJsData($key, $value);
        }
        return $this;
    }

    /**
     * Enqueue plugin's CSS stylesheet.
     * @param string $pluginRelatedPath Can be in full form like "css/bla.css" or in short like "bla"
     * @return self
     */
    protected function enqueueStyle($pluginRelatedPath)
    {
        if (substr($pluginRelatedPath, -4) != '.css') {
            $pluginRelatedPath = 'css/' . $pluginRelatedPath . '.css';
        }
        $name = $this->getResourceNameFromPluginRelatedPath($pluginRelatedPath, '.css');
        $this->wp->enqueuePluginStyle($name, $pluginRelatedPath);
        return $this;
    }

    /**
     * Enqueue plugin's JS script.
     *
     * Comparing to `enqueuePluginScript` it has no 1st param, last params are switched and defaults changed.
     *
     * @see WpTesting_WordPressFacade::enqueuePluginScript
     *
     * @param string $pluginRelatedPath Can be in full form like "js/do-something.js" or in short like "do-something"
     * @param array $dependencies
     * @param boolean $isInFooter
     * @param string $version
     * @return self
     */
    protected function enqueueScript($pluginRelatedPath, array $dependencies = array(), $isInFooter = true, $version = false)
    {
        if (substr($pluginRelatedPath, -3) != '.js') {
            $pluginRelatedPath = 'js/' . $pluginRelatedPath . '.js';
        }
        $name = $this->getResourceNameFromPluginRelatedPath($pluginRelatedPath, '.js');
        $this->wp->enqueuePluginScript($name, $pluginRelatedPath, $dependencies, $version, $isInFooter);
        return $this;
    }

    /**
     * @param string $pluginRelatedPath
     * @param string $extension
     * @return string
     */
    private function getResourceNameFromPluginRelatedPath($pluginRelatedPath, $extension)
    {
        $name = basename($pluginRelatedPath, $extension);
        $name = str_replace('-', '_', $name);
        $name = $this->resourceIdPrefix . $name;
        return $name;
    }

    private function getResourcePrefix($mainClassStart, $abbr)
    {
        $parts  = explode('_', get_class($this));
        $prefix = reset($parts);
        if ($prefix == $mainClassStart) {
            return $abbr;
        }
        $prefix = str_replace($mainClassStart, $abbr . '_' , $prefix);
        return strtolower($prefix) . '_';
    }

    /**
     * Register common used scripts for future dependencies
     * @return self
     */
    protected function registerScripts()
    {
        $e       = array();
        $scripts = array(
            array('detect-javascript', 'js/detect-javascript.js',                  $e, '1.0'),
            array('lodash-source',     'js/vendor/lodash/lodash.compat.min.js',    $e, '2.4.1'),
            array('lodash',            'js/vendor/lodash/lodash.no-conflict.js',   array('lodash-source')),
            array('npm-stub',          'js/vendor/npm/stub.js',                    $e, '1.0'),
            array('base64',            'js/vendor/dankogai/base64.min.js',         $e, '2.1.7'),
            array('pnegri_uuid',       'vendor/pnegri/uuid-js/lib/uuid.js',        array('npm-stub')),
            array('samyk_swfobject',   'vendor/samyk/evercookie/js/swfobject-2.2.min.js'),
            array('samyk_evercookie',  'vendor/samyk/evercookie/js/evercookie.js', array('samyk_swfobject')),
            array('field_selection',   'js/vendor/kof/field-selection.js'),
            array('json3',             'js/vendor/bestiejs/json3.min.js'),
            array('angular',           'js/vendor/google/angular/angular.min.js',  $e, '1.3.15'),
            array('webshim',           'js/vendor/afarkas/webshim/polyfiller.js',  array('jquery'), '1.15.7'),

            // Vector graphics for diagramming
            array('raphael',               'js/vendor/dmitrybaranovskiy/raphael-min.js',   $e, '2.0.2'),
            array('raphael-diagrams',      'js/vendor/dmitrybaranovskiy/g.raphael.js',     array('raphael'), '0.51'),
            array('raphael-line-diagram',  'js/vendor/dmitrybaranovskiy/g.line.js',        array('raphael-diagrams'), '0.51'),
            array('raphael-scale',         'js/vendor/zevanrosser/scale.raphael.js',       array('raphael'), '0.8'),
        );

        foreach ($scripts as $script) {
            $script += array('', '', array(), false);
            list($name, $pluginRelatedPath, $dependencies, $version) = $script;
            $this->wp->registerPluginScript($name, $pluginRelatedPath, $dependencies, $version);
        }

        return $this;
    }

    protected function output($__template, $__params = array())
    {
        if (substr($__template, -4) != '.php') {
            $__template = $this->templateRoot . $__template . '.php';
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

    protected function getRequestValue($key, $castTo = null)
    {
        return fRequest::get($key, $castTo);
    }

    /**
     * For example: /path?param=value /path/
     * @return string
     */
    protected function getCurrentUrl()
    {
        return fURL::getWithQueryString();
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
    protected function getClientIp()
    {
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
     * Get visitor's browser user agent string
     *
     * @return string
     */
    protected function getUserAgent()
    {
        return $this->getEnv('HTTP_USER_AGENT');
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

    protected function toJson($object)
    {
        if ($object instanceof fRecordSet) {
            return $this->toJson(array_values($object->getRecords()));
        }
        if (is_array($object)) {
            $result = array();
            foreach ($object as $key => $value) {
                $result[$key] = $this->toJson($value);
            }
            return $result;
        }
        if ($object instanceof JsonSerializable) {
            return $object->jsonSerialize();
        }
        return $object;
    }

    /**
     * Checks whether queried object type same as passed
     * @param string $type
     * @return boolean
     */
    protected function isPostType($type)
    {
        $object = $this->wp->getQuery()->get_queried_object();
        return (is_object($object) && !empty($object->post_type) && $object->post_type == $type);
    }

    /**
     * Retrieve user meta field for current logged in user
     *
     * @param string $key
     * @return string
     */
    protected function getCurrentUserMeta($key)
    {
        return $this->wp->getUserMeta($this->wp->getCurrentUserId(), $key, true);
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
