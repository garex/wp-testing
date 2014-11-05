<?php

class WpTesting_Doer_WordPressEntitiesRegistrator extends WpTesting_Doer_AbstractDoer
{

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        parent::__construct($wp);

        $wp->registerPostType('wpt_test', array(
            'labels'   => $this->generateLabels('test'),
            'description'   => 'Instrument to measure unobserved constructs (latent variables).  Typically it is a series of tasks or problems that the respondent has to solve.',
            'public'        => true,
            'menu_position' => 5,
            'menu_icon'     => $this->isWordPressAlready('3.8') ? 'dashicons-editor-paste-text' : null,
            'supports'      => array(
                'title',
                'editor',
                'thumbnail',
                'comments',
            ),
            'taxonomies'    => array(
                'wpt_answer',
                'wpt_scale',
                'wpt_result',
                'wpt_category',
            ),
            'has_archive'   => true,
            'rewrite'       => array(
                'slug'   => 'test',
                'pages'  => false,
            ),
            'can_export' => true,
        ));

        $this
            ->addTaxonomy('answer')
            ->addTaxonomy('scale')
            ->addTaxonomy('result')
            ->addTaxonomy('category', array(
                'labels'            => $this->generateLabels('category', 'categories'),
                'public'            => true,
                'show_in_nav_menus' => true,
                'rewrite'           => array(
                    'slug' => 'test-category',
                ),
            ))
        ;

        $this->wp->getRewrite()->flush_rules();
    }

    protected function generateLabels($name, $pluralName = null)
    {
        $name       = ucfirst($name);
        $pluralName = ($pluralName) ? $pluralName : $name . 's';
        $pluralName = ucfirst($pluralName);
        return array(
            'name'               => "Test $pluralName",
            'singular_name'      => "Test $name",
            'add_new_item'       => "Add New $name",
            'edit_item'          => "Edit $name",
            'view_item'          => "View $name",
            'search_items'       => "Search $pluralName",
            'parent_item_colon'  => "Parent $name:",

            'menu_name'          => "$pluralName",
            'all_items'          => "All $pluralName",
            'update_item'        => "Update $name",
            'new_item_name'      => "New $name Name",
            'parent_item'        => "Parent $name",

            'new_item'           => "New $name",
            'not_found'          => "No $pluralName found",
            'not_found_in_trash' => "No $pluralName found in Trash",
        );
    }

    protected function addTaxonomy($name, $parameters = array())
    {
        $this->wp->registerTaxonomy('wpt_' . $name, array('wpt_test'),  $parameters + array(
            'hierarchical'      => true,
            'labels'            => $this->generateLabels($name),
            'public'            => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'sort'              => true,
        ));

        return $this;
    }

}
