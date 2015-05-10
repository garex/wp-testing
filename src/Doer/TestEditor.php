<?php

class WpTesting_Doer_TestEditor extends WpTesting_Doer_AbstractDoer
{

    /**
     * Current test taxonomies, broken by taxonomy
     * @var array
     */
    private $selectedTermsIds = array();

    /**
     * @param WP_Screen $screen
     * @return WpTesting_Doer_TestEditor
     */
    public function customizeUi($screen)
    {
        if (!$this->isTestScreen($screen)) {
            return $this;
        }
        $this->wp->doAction('wp_testing_editor_customize_ui_before');
        $this->registerScripts()
            ->enqueueStyle('admin')
            ->enqueueScript('test-edit-fix-styles', array('jquery'))
            ->enqueueScript('test-edit-formulas',   array('jquery', 'field_selection'))
            ->enqueueScript('test-quick-scores',    array('jquery', 'lodash'))
            ->enqueueScript('test-quick-questions', array('jquery', 'json3', 'base64'))
            ->enqueueScript('test-edit-answers',    array('jquery'))
            ->enqueueScript('test-add-answers',     array('jquery', 'lodash'))
            ->enqueueScript('test-sort-taxonomies', array('jquery', 'jquery-ui-sortable'))
        ;
        $this->wp
            ->addAction('post_submitbox_misc_actions', array($this, 'renderSubmitMiscActions'))
            ->addAction('media_buttons',               array($this, 'renderContentEditorButtons'))
            ->addAction('add_meta_boxes_wpt_test', array($this, 'setDefaultMetaboxesOrder'))
            ->addMetaBox('wpt_test_page_options', __('Test Page Options', 'wp-testing'),
                array($this, 'renderTestPageOptions'), 'wpt_test', 'side', 'core')
            ->addMetaBox('wpt_result_page_options', __('Result Page Options', 'wp-testing'),
                array($this, 'renderResultPageOptions'), 'wpt_test', 'side', 'core')
            ->addMetaBox('wpt_edit_questions', __('Edit Questions and Scores', 'wp-testing'),    array($this, 'renderEditQuestions'), 'wpt_test')
            ->addMetaBox('wpt_add_questions',  __('Add New Questions', 'wp-testing'), array($this, 'renderAddQuestions'),  'wpt_test')
            ->addMetaBox('wpt_edit_formulas',  __('Edit Formulas', 'wp-testing'),     array($this, 'renderEditFormulas'),  'wpt_test')
            ->addAction('save_post',     array($this, 'saveTest'), 10, 2)
        ;
        // Respect metabox sort order
        if ($this->isWordPressAlready('3.4')) {
            $this->wp->addFilter('wp_terms_checklist_args', array($this, 'filterTermsChecklistArgs'), 10, 2);
        } else {
            $this->wp->addFilter('wp_get_object_terms', array($this, 'filterForceSortObjectTerms'), 10, 4);
        }
        $this->wp->doAction('wp_testing_editor_customize_ui_after');
        return $this;
    }

    /**
     * Allow more HTML tags in taxonomies
     * @return WpTesting_Doer_TestEditor
     */
    public function allowMoreHtmlInTaxonomies()
    {
        if (!$this->isTestTaxonomy()) {
            return $this;
        }

        if ($this->isWordPressAlready('3.5')) {
            $this->wp->addFilter('wp_kses_allowed_html', array($this, 'filterAllowedHtmlInTaxonomies'), 10, 2);
        } else {
            $this->wp->removeFilter('pre_term_description', 'wp_filter_kses');
        }

        return $this;
    }

    /**
     * @param WP_Post $post
     */
    public function setDefaultMetaboxesOrder($post)
    {
        $boxes = $this->wp->getMetaBoxes('wpt_test', 'side', 'core');
        $boxes = $this->arrayMoveItemAfter($boxes, 'wpt_result_page_options', 'submitdiv');
        $boxes = $this->arrayMoveItemAfter($boxes, 'wpt_test_page_options', 'submitdiv');
        $this->wp->setMetaBoxes($boxes, 'wpt_test', 'side', 'core');
    }

    public function filterTermsChecklistArgs($args, $postId = null)
    {
        $taxonomy = $args['taxonomy'];
        if (!in_array($taxonomy, array('wpt_answer', 'wpt_scale', 'wpt_result'))) {
            return $args;
        }
        if (empty($postId)) {
            return $args;
        }
        $args['selected_cats'] = $this->wp->getObjectTerms($postId, $taxonomy, array(
            'taxonomy' => $taxonomy,
            'fields'   => 'ids',
            'orderby'  => 'term_order',
        ));
        $this->selectedTermsIds[$taxonomy] = $args['selected_cats'];
        $this->wp->addFilterOnce('get_terms_orderby', array($this, 'filterTermsOrderBy'), 10, 3);
        return $args;
    }

    public function filterForceSortObjectTerms($terms, $objectIds, $taxonomies, $args)
    {
        if (!isset($args['taxonomy']) || !in_array($args['taxonomy'], array('wpt_answer', 'wpt_scale', 'wpt_result'))) {
            return $terms;
        }
        $model = new WpTesting_Model_Taxonomy();
        $terms = $model->sortTermIdsByTermOrder($objectIds, $terms);
        $this->selectedTermsIds[$args['taxonomy']] = $terms;
        $this->wp->addFilterOnce('get_terms_orderby', array($this, 'filterTermsOrderBy'), 10, 3);
        return $terms;
    }

    public function filterTermsOrderBy($orderBy, $args, $taxonomies = null)
    {
        if (is_null($taxonomies)) { // Old WP versions workaround
            $this->wp->removeFilter('get_terms_orderby', array($this, 'filterTermsOrderBy'), 10, 3);
            end($this->selectedTermsIds);
            $taxonomies = array(key($this->selectedTermsIds));
        }

        $isSort = true
            && isset($taxonomies[0])
            && !empty($this->selectedTermsIds[$taxonomies[0]])
            && $args['orderby'] == 'name';

        if (!$isSort) {
            return $orderBy;
        }

        $ids   = implode(',', $this->selectedTermsIds[$taxonomies[0]]);
        $order = $args['order'];
        return "FIELD(t.term_id, $ids) $order, name";
    }

    public function filterAllowedHtmlInTaxonomies($allowedTags, $context)
    {
        $newTags = array(
            'h1', 'h2', 'h3', 'h4', 'h5',
            'ol', 'ul', 'li',
            'hr', 'img',
        );
        foreach ($newTags as $tag) {
            $allowedTags[$tag] = array('class' => true);
        }
        $allowedTags['img']['src'] = true;
        return $allowedTags;
    }

    public function renderSubmitMiscActions()
    {
        // Set metadata defaults
        $isPublishOnHome = $this->wp->getCurrentPostMeta('wpt_publish_on_home');
        if ($isPublishOnHome == '') {
            $isPublishOnHome = '1';
        }
        $this->output('Test/Editor/submit-misc-actions', array(
            'isPublishOnHome' => $isPublishOnHome,
        ));
    }

    public function renderContentEditorButtons($editorId)
    {
        if ('content' != $editorId) {
            return;
        }
        $this->output('Test/Editor/content-editor-buttons');
    }

    /**
     * @param WP_Post $item
     */
    public function renderTestPageOptions($item)
    {
        $options = array(
            'wpt_test_page_show_progress_meter' => array(
                'default' => '1',
                'title'   => __('Show in title percentage of questions that respondent already answered', 'wp-testing'),
            ),
            'wpt_test_page_one_question_per_step' => array(
                'default' => '0',
                'title'   => __('Show one question per page', 'wp-testing'),
            ),
            'wpt_test_page_multiple_answers' => array(
                'default' => '0',
                'title'   => __('Allow multiple answers per question', 'wp-testing'),
            ),
            'wpt_test_page_reset_answers_on_back' => array(
                'default' => '0',
                'title'   => __('Reset respondent answers when "Back" button pressed', 'wp-testing'),
            ),
            'wpt_test_page_submit_button_caption' => array(
                'default' => '',
                'title'   => __('Button caption', 'wp-testing'),
                'type'    => 'text',
                'placeholder' => __('Get Test Results', 'wp-testing'),
            ),
        );

        $this->renderMetaboxOptions($options);
    }

    /**
     * @param WP_Post $item
     */
    public function renderResultPageOptions($item)
    {
        $options = array(
            'wpt_result_page_show_scales_diagram' => array(
                'default' => '0',
                'title'   => __('Show scales chart', 'wp-testing'),
            ),
            'wpt_result_page_show_scales' => array(
                'default' => '1',
                'title'   => __('Show scales', 'wp-testing'),
            ),
            'wpt_result_page_sort_scales_by_score' => array(
                'default' => '0',
                'title'   => __('Sort scales by score', 'wp-testing'),
            ),
            'wpt_result_page_show_test_description' => array(
                'default' => '0',
                'title'   => __('Show test description', 'wp-testing'),
            ),
        );

        $this->renderMetaboxOptions($options);
    }

    /**
     * @param WP_Post $item
     */
    public function renderEditQuestions($item)
    {
        $test = $this->createTest($item);
        $this->output('Test/Editor/edit-questions', array(
            'scales'              => $test->buildScalesWithRange(),
            'answers'             => $test->buildGlobalAnswers(),
            'questions'           => $test->buildQuestionsWithAnswersAndScores(),
            'isWarnOfSettings'    => $test->isWarnOfSettings(),
            'memoryWarnSettings'  => $test->getMemoryWarnSettings(),
            'isUnderApache'       => $this->isUnderApache(),
            'canEditScores'       => $test->canEditScores(),
        ));
    }

    /**
     * @param WP_Post $item
     */
    public function renderAddQuestions($item)
    {
        $test = $this->createTest($item);
        $this->output('Test/Editor/add-questions', array(
            'addNewCount' => WpTesting_Model_Question::ADD_NEW_COUNT,
            'startFrom'   => $test->buildQuestions()->count(),
            'test'        => $test,
        ));
    }

    /**
     * @param WP_Post $item
     */
    public function renderEditFormulas($item)
    {
        $test = $this->createTest($item);
        $this->output('Test/Editor/edit-formulas', array(
            'results'    => $test->buildResults(),
            'variables'  => $test->buildFormulaVariables(),
        ));
    }

    /**
     * @param integer $id
     * @param WP_Post $item
     */
    public function saveTest($id, $item)
    {
        $test = $this->createTest($item);
        if (!$test->getId()) {
            return;
        }

        $metaOptions = array(
            'wpt_publish_on_home',
            'wpt_test_page_submit_button_caption',
            'wpt_test_page_reset_answers_on_back',
            'wpt_test_page_show_progress_meter',
            'wpt_test_page_one_question_per_step',
            'wpt_test_page_multiple_answers',
            'wpt_result_page_show_scales_diagram',
            'wpt_result_page_show_scales',
            'wpt_result_page_sort_scales_by_score',
            'wpt_result_page_show_test_description',
        );

        // Update metadata only when we have appropriate keys
        $isFullEdit      = (!is_null($this->getRequestValue($metaOptions[0])));
        if (!$isFullEdit) {
            return;
        }

        foreach ($metaOptions as $metaOptionKey) {
            $metaOptionValue = $this->getRequestValue($metaOptionKey);
            $this->wp->updatePostMeta($test->getId(), $metaOptionKey, $metaOptionValue);
        }

        try {
            $test->storeAll();
        } catch (fValidationException $e) {
            $title = __('Test data not saved', 'wp-testing');
            $this->wp->dieMessage(
                $this->render('Test/Editor/admin-message', array(
                    'title'   => $title,
                    'content' => $e->getMessage(),
                )),
                $title,
                array('back_link' => true)
            );
        }
    }

    /**
     * Do we currently at tests?
     *
     * @param WP_Screen $screen
     * @return boolean
     */
    private function isTestScreen($screen)
    {
        if (!empty($screen->post_type) && $screen->post_type == 'wpt_test') {
            return true;
        }
        if ($this->isWordPressAlready('3.3')) {
            return false;
        }

        // WP 3.2 workaround
        if ($this->isPost() && $this->getRequestValue('post_type') == 'wpt_test') {
            return true;
        }

        $id = $this->getRequestValue('post');
        if (!$id) {
            return false;
        }
        $test   = $this->createTest($id);
        $isTest = ($test->getId()) ? true : false;
        $test->reset();
        return $isTest;
    }

    private function isTestTaxonomy()
    {
        return preg_match('/^wpt_/', $this->getRequestValue('taxonomy'));
    }

    private function isUnderApache()
    {
        if (empty($_SERVER['SERVER_SOFTWARE'])) {
            return false;
        }
        return preg_match('/apache|httpd/i', $_SERVER['SERVER_SOFTWARE']);
    }

    private function renderMetaboxOptions($options)
    {
        foreach ($options as $key => $option) {
            $option['value'] = $this->wp->getCurrentPostMeta($key);
            if ($option['value'] == '') {
                $option['value'] = $option['default'];
            }
            if (empty($option['type'])) {
                $option['type'] = 'checkbox';
            }
            if (empty($option['placeholder'])) {
                $option['placeholder'] = '';
            }
            $options[$key] = $option;
        }

        $this->output('Test/Editor/metabox-options', array(
            'options' => $options,
        ));
    }
}
