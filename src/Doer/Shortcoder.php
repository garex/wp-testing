<?php

abstract class WpTesting_Doer_Shortcoder extends WpTesting_Doer_AbstractDoer
{

    /**
     * @var WpTesting_Facade_IORM
     */
    private $orm;

    public function __construct(WpTesting_WordPressFacade $wp, WpTesting_Facade_IORM $ormAware)
    {
        parent::__construct($wp);
        $this->orm = $ormAware;
    }

    public function renderShortcode(array $attributes, $content, $tag)
    {
        try {
            $shortcode = $this->createShortcode($attributes);
            return $this->render(
                $this->chooseTemplate($shortcode),
                $shortcode->getDataForTemplate($this->orm)
            );
        } catch (PHPUnit_Framework_Error $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->renderException($e, $tag);
        }
    }

    protected function renderException(Exception $e, $tag)
    {
        return  $this->render(
            'Shortcode/Exception/exception',
            array(
                'name'    => $tag,
                'message' => $e->getMessage(),
                'class'   => get_class($e),
            )
        );
    }

    /**
     * @param array $attributes
     * @return WpTesting_Model_Shortcode
     */
    abstract protected function createShortcode(array $attributes);

    /**
     * @param WpTesting_Model_Shortcode $shortcode
     * @return string
     */
    abstract protected function chooseTemplate(WpTesting_Model_Shortcode $shortcode);
}
