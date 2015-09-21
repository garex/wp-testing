<?php

abstract class WpTesting_Doer_Shortcoder extends WpTesting_Doer_AbstractDoer
{

    const ACTION_RENDER_TEMPLATE = 'wp_testing_shortcoder_%s_render_shortcode_%s';

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
            $uniqueName = $shortcode->getUniqueName();
            $this->wp->doAction(sprintf(self::ACTION_RENDER_TEMPLATE, 'before', $uniqueName), $shortcode);
            $this->wp
                ->addAction(sprintf(self::ACTION_RENDER_TEMPLATE, 'before', $uniqueName), array($this, 'avoidRecursion'))
                ->addAction(sprintf(self::ACTION_RENDER_TEMPLATE, 'after',  $uniqueName), array($this, 'clearAvoidRecursion'))
            ;
            $template  = $this->chooseTemplate($shortcode);
            $data      = $this->getData($shortcode);
            $result    = $this->render($template, $data);
            $this->wp->doAction(sprintf(self::ACTION_RENDER_TEMPLATE, 'after', $uniqueName), $shortcode);
            return $result;
        } catch (PHPUnit_Framework_Error $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->renderException($e, $tag);
        }
    }

    public function avoidRecursion(WpTesting_Model_Shortcode $shortcode)
    {
        throw new LogicException(sprintf('Shortcode "%s" includes itself', $shortcode->getUniqueName()));
    }

    public function clearAvoidRecursion(WpTesting_Model_Shortcode $shortcode)
    {
        $uniqueName = $shortcode->getUniqueName();
        $this->wp
            ->removeAction(sprintf(self::ACTION_RENDER_TEMPLATE, 'before', $uniqueName), array($this, 'avoidRecursion'))
            ->removeAction(sprintf(self::ACTION_RENDER_TEMPLATE, 'after',  $uniqueName), array($this, 'clearAvoidRecursion'))
        ;
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

    protected function getData(WpTesting_Model_Shortcode $shortcode)
    {
        return $shortcode->getDataForTemplate($this->orm);
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
