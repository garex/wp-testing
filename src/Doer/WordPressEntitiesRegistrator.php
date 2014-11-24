<?php

class WpTesting_Doer_WordPressEntitiesRegistrator extends WpTesting_Doer_AbstractDoer
{

    public function __construct(WpTesting_WordPressFacade $wp)
    {
        parent::__construct($wp);

        $wp->registerPostType('wpt_test', array(
            'labels'        => array(
                'name'               => __('Tests', 'wp-testing'),
                'singular_name'      => __('Test', 'wp-testing'),
                'add_new'            => _x('Add New', 'test', 'wp-testing'),
                'add_new_item'       => __('Add New Test', 'wp-testing'),
                'edit_item'          => __('Edit Test', 'wp-testing'),
                'view_item'          => __('View Test', 'wp-testing'),
                'search_items'       => __('Search Tests', 'wp-testing'),
                'parent_item_colon'  => __('Parent Test:', 'wp-testing'),

                'menu_name'          => __('Tests', 'wp-testing'),
                'all_items'          => __('All Tests', 'wp-testing'),
                'update_item'        => __('Update Test', 'wp-testing'),
                'new_item_name'      => __('New Test Name', 'wp-testing'),
                'parent_item'        => __('Parent Test', 'wp-testing'),

                'new_item'           => __('New Test', 'wp-testing'),
                'not_found'          => __('No Tests found', 'wp-testing'),
                'not_found_in_trash' => __('No Tests found in Trash', 'wp-testing'),
            ),
            'description'   => __('Instrument to measure unobserved constructs (latent variables). Typically it is a series of tasks or problems that the respondent has to solve.', 'wp-testing'),
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
                'category',
                'post_tag',
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
            ->addTaxonomy('answer', array(
                'labels' => array(
                    'name'               => __('Test Answers', 'wp-testing'),
                    'singular_name'      => __('Test Answer', 'wp-testing'),
                    'add_new_item'       => __('Add New Answer', 'wp-testing'),
                    'edit_item'          => __('Edit Answer', 'wp-testing'),
                    'view_item'          => __('View Answer', 'wp-testing'),
                    'search_items'       => __('Search Answers', 'wp-testing'),
                    'parent_item_colon'  => __('Parent Answer:', 'wp-testing'),

                    'menu_name'          => __('Answers', 'wp-testing'),
                    'all_items'          => __('All Answers', 'wp-testing'),
                    'update_item'        => __('Update Answer', 'wp-testing'),
                    'new_item_name'      => __('New Answer Name', 'wp-testing'),
                    'parent_item'        => __('Parent Answer', 'wp-testing'),

                    'new_item'           => __('New Answer', 'wp-testing'),
                    'not_found'          => __('No Answers found', 'wp-testing'),
                    'not_found_in_trash' => __('No Answers found in Trash', 'wp-testing'),
                ),
            ))
            ->addTaxonomy('scale', array(
                'labels' => array(
                    'name'               => __('Test Scales', 'wp-testing'),
                    'singular_name'      => __('Test Scale', 'wp-testing'),
                    'add_new_item'       => __('Add New Scale', 'wp-testing'),
                    'edit_item'          => __('Edit Scale', 'wp-testing'),
                    'view_item'          => __('View Scale', 'wp-testing'),
                    'search_items'       => __('Search Scales', 'wp-testing'),
                    'parent_item_colon'  => __('Parent Scale:', 'wp-testing'),

                    'menu_name'          => __('Scales', 'wp-testing'),
                    'all_items'          => __('All Scales', 'wp-testing'),
                    'update_item'        => __('Update Scale', 'wp-testing'),
                    'new_item_name'      => __('New Scale Name', 'wp-testing'),
                    'parent_item'        => __('Parent Scale', 'wp-testing'),

                    'new_item'           => __('New Scale', 'wp-testing'),
                    'not_found'          => __('No Scales found', 'wp-testing'),
                    'not_found_in_trash' => __('No Scales found in Trash', 'wp-testing'),
                ),
            ))
            ->addTaxonomy('result', array(
                'labels' => array(
                    'name'               => __('Test Results', 'wp-testing'),
                    'singular_name'      => __('Test Result', 'wp-testing'),
                    'add_new_item'       => __('Add New Result', 'wp-testing'),
                    'edit_item'          => __('Edit Result', 'wp-testing'),
                    'view_item'          => __('View Result', 'wp-testing'),
                    'search_items'       => __('Search Results', 'wp-testing'),
                    'parent_item_colon'  => __('Parent Result:', 'wp-testing'),

                    'menu_name'          => __('Results', 'wp-testing'),
                    'all_items'          => __('All Results', 'wp-testing'),
                    'update_item'        => __('Update Result', 'wp-testing'),
                    'new_item_name'      => __('New Result Name', 'wp-testing'),
                    'parent_item'        => __('Parent Result', 'wp-testing'),

                    'new_item'           => __('New Result', 'wp-testing'),
                    'not_found'          => __('No Results found', 'wp-testing'),
                    'not_found_in_trash' => __('No Results found in Trash', 'wp-testing'),
                ),
            ))
            ->addTaxonomy('category', array(
                'labels'            => array(
                    'name'               => __('Test Categories', 'wp-testing'),
                    'singular_name'      => __('Test Category', 'wp-testing'),
                    'add_new_item'       => __('Add New Category', 'wp-testing'),
                    'edit_item'          => __('Edit Category', 'wp-testing'),
                    'view_item'          => __('View Category', 'wp-testing'),
                    'search_items'       => __('Search Categories', 'wp-testing'),
                    'parent_item_colon'  => __('Parent Category:', 'wp-testing'),

                    'menu_name'          => __('Categories', 'wp-testing'),
                    'all_items'          => __('All Categories', 'wp-testing'),
                    'update_item'        => __('Update Category', 'wp-testing'),
                    'new_item_name'      => __('New Category Name', 'wp-testing'),
                    'parent_item'        => __('Parent Category', 'wp-testing'),

                    'new_item'           => __('New Category', 'wp-testing'),
                    'not_found'          => __('No Categories found', 'wp-testing'),
                    'not_found_in_trash' => __('No Categories found in Trash', 'wp-testing'),
                ),
                'public'            => true,
                'show_in_nav_menus' => true,
                'rewrite'           => array(
                    'slug' => 'test-category',
                ),
            ))
        ;

        $this->wp->getRewrite()->flush_rules();
    }

    protected function addTaxonomy($name, $parameters = array())
    {
        $this->wp->registerTaxonomy('wpt_' . $name, array('wpt_test'),  $parameters + array(
            'hierarchical'      => true,
            'public'            => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'sort'              => true,
        ));

        return $this;
    }

}
