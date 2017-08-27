<?php

abstract class WpTesting_Doer_AbstractEditor extends WpTesting_Doer_AbstractDoer
{
    /**
     * Post meta options, groupped by name, that is filter tag also.
     *
     * Can be rendered by name.
     *
     * @var array
     */
    private $metaOptions = array();

    /**
     * @var string
     */
    private $firstMetaOptionsKey = null;

    /**
     * @return boolean
     */
    protected function hasMetaOptionsInRequest()
    {
        if (empty($this->metaOptions)) {
            return false;
        }

        return !is_null($this->getRequestValue($this->firstMetaOptionsKey));
    }

    public function updateMetaOptions(WpTesting_Model_AbstractParent $post)
    {
        foreach ($this->metaOptions as $metaOptions) {
            foreach ($metaOptions as $key => $metaOption) {
                if (isset($metaOption['type']) && $metaOption['type'] == 'header') {
                    continue;
                }
                $value = $this->getRequestValue($key);
                $this->wp->updatePostMeta($post->getId(), $key, $value);
            }
        }
    }

    /**
     * @param string $groupName
     * @param array $options
     *
     * @return self
     */
    protected function addMetaOptions($groupName, array $options)
    {
        $this->metaOptions[$groupName] = $this->wp->applyFilters($groupName, $options);

        if (empty($this->firstMetaOptionsKey)) {
            $this->firstMetaOptionsKey = key($options);
        }

        return $this;
    }

    /**
     * @param string $groupName
     */
    protected function renderMetaOptions($groupName)
    {
        $options = $this->metaOptions[$groupName];

        foreach ($options as $key => $option) {
            $option += array(
                'default' => '',
                'attributes' => '',
            );
            if (!isset($option['value'])) {
                $option['value'] = $this->wp->getCurrentPostMeta($key);
            }
            if (isset($option['defaultOnAdd']) && $this->isAddAction()) {
                $option['default'] = $option['defaultOnAdd'];
            }
            if ($option['value'] == '') {
                $option['value'] = $option['default'];
            }
            if (empty($option['type'])) {
                $option['type'] = 'checkbox';
            }
            if (empty($option['placeholder'])) {
                $option['placeholder'] = '';
            }
            if (empty($option['break'])) {
                $option['break'] = false;
            }
            $options[$key] = $option;
        }

        $root = dirname(dirname(__FILE__)) . '/Template/';
        $this->output($root . 'AbstractEditor/metabox-options.php', array(
            'options' => $options,
        ));
    }

    protected function jsonResponse(array $data)
    {
        header('Content-type: application/json');

        $j = new WpTesting_Component_Json();
        echo $j->encode($data);

        exit;
    }

    private function isAddAction()
    {
        $screen = $this->wp->getCurrentScreen();
        if (!$screen instanceof WP_Screen) {
            return null;
        }
        return 'add' == $screen->action;
    }
}
