<?php

class WpTesting_Doer_TestEditor extends WpTesting_Doer_AbstractEditor
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
        $isTestEditor = $this->isTestScreen($screen, 'id');
        if ($isTestEditor || $this->isTestScreen($screen, 'post_type')) {
            $this->wp->doAction('wp_testing_editor_tests_screen');
        }
        if (!$isTestEditor) {
            return $this;
        }
        $test = $this->createTest($this->getRequestValue('post'));
        $this->wp->doAction('wp_testing_editor_customize_ui_before');
        $this->wp->enqueueStyle('wp-jquery-ui-dialog');
        $this->registerScripts()
            ->upgradeJqueryForOldWordPress()
            ->enqueueStyle('admin')
            ->enqueueStyle('maximize')
            ->enqueueScript('test-edit-maximize-metaboxes', array('maximize'))
            ->enqueueScript('test-edit-fix-styles', array('jquery'))
            ->enqueueScript('test-sort-taxonomies', array('jquery', 'jquery-ui-sortable'))
            ->enqueueScript('test-edit-ajax-save',  array('jquery', 'jquery-ui-dialog'))
            ->enqueueScript('app/app.module',       array('webshim', 'angular', 'garex_sorted_map', 'caretaware'))
        ;
        // $this->enqueueScript('vendor/pkaminski/digest-hud')->enqueueScript('app/app.module.debug');
        $this
            ->enqueueScript('app/base/baseOwnerable.model')
            ->enqueueScript('app/base/baseObservable.model', array('asevented'))
            ->enqueueScript('app/base/baseCollection.model')
            ->enqueueScript('app/scores/scoreCollection.model')
            ->enqueueScript('app/questionsAnswers/answerCollection.model')
            ->enqueueScript('app/questionsAnswers/questionCollection.model')
            ->enqueueScript('app/questionsAnswers/questions.service')
            ->enqueueScript('app/services/highlight.service')
            ->enqueueScript('app/directives/set.focus.directive')
            ->enqueueScript('app/questionsAnswers/questionsAnswers.edit.controller')
            ->enqueueScript('app/questionsAnswers/quickFill.edit.controller')
            ->enqueueScript('app/scores/scaleCollection.model')
            ->enqueueScript('app/scores/scales.service')
            ->enqueueScript('app/scores/scores.edit.controller')
            ->enqueueScript('app/questionsTree/questionTree.model')
            ->enqueueScript('app/questionsTree/questionTree.service')
            ->enqueueScript('app/questionsTree/questionTree.edit.controller')
            ->enqueueScript('app/formulas/variableCollection.model')
            ->enqueueScript('app/formulas/variables.service')
            ->enqueueScript('app/formulas/formula.toolbar.directive')
            ->enqueueScript('app/formulas/formula.model')
            ->enqueueScript('app/formulas/resultCollection.model')
            ->enqueueScript('app/formulas/results.service')
            ->enqueueScript('app/formulas/formulas.edit.controller')
            ->addJsData('questions',     $this->toJson($test->buildQuestionsWithAnswers()))
            ->addJsData('globalAnswers', $this->toJson($test->buildGlobalAnswers()))
            ->addJsData('scales',        $this->toJson($test->buildScales()))
            ->addJsData('results',       $this->toJson($test->buildResults()))
            ->addJsData('variables',     $this->toJson(array_values($test->buildPublicFormulaVariables())))
            ->addJsData('locale', array(
                'maximize' => __('Maximize', 'wp-testing'),
                'minimize' => __('Minimize', 'wp-testing'),
                'OK' => $this->wp->translate('OK'),
                'comparision' => __('Comparision', 'wp-testing'),
                'operator'    => __('Operator', 'wp-testing'),
            ))
        ;

        $this->wp->doAction('wp_testing_editor_customize_ui_before_angular_run');
        $this->enqueueScript('app/app.module.run');

        $this->wp
            ->addAction('post_submitbox_misc_actions', array($this, 'renderSubmitMiscOptions'))
            ->addAction('media_buttons',               array($this, 'renderContentEditorButtons'))
            ->addAction('add_meta_boxes_wpt_test', array($this, 'setDefaultMetaboxesOrder'))
            ->addMetaBox('wpt_test_page_options', __('Test Page Options', 'wp-testing'),
                array($this, 'renderTestPageOptions'), 'wpt_test', 'side', 'core')
            ->addMetaBox('wpt_result_page_options', __('Result Page Options', 'wp-testing'),
                array($this, 'renderResultPageOptions'), 'wpt_test', 'side', 'core')
            ->addMetaBox('wpt_edit_questions_answers', __('Edit Questions and Answers', 'wp-testing'),    array($this, 'renderEditQuestionsAnswers'), 'wpt_test')
            ->addMetaBox('wpt_edit_scores', __('Edit Scores', 'wp-testing'),    array($this, 'renderEditScores'), 'wpt_test')
            ->addMetaBox('wpt_quick_fill_scores', __('Quick Fill Scores', 'wp-testing'),    array($this, 'renderQuickFillScores'), 'wpt_test')
            ->addMetaBox('wpt_edit_formulas',  __('Edit Formulas', 'wp-testing'),     array($this, 'renderEditFormulas'),  'wpt_test')
            ->addAction('wp_testing_test_store_all_before', array($this, 'updateMetaOptions'))
            ->addAction('wp_insert_post', array($this, 'saveTest'), 99, 2)
        ;
        $this
            ->addSubmitMiscOptions()
            ->addTestPageOptions()
            ->addResultPageOptions()
        ;
        // Respect metabox sort order
        if ($this->isWordPressAlready('3.4')) {
            $this->wp->addFilter('wp_terms_checklist_args', array($this, 'filterTermsChecklistArgs'), WpTesting_WordPress_IPriority::PRIORITY_DEFAULT, 2);
        } else {
            $this->wp->addFilter('wp_get_object_terms', array($this, 'filterForceSortObjectTerms'), WpTesting_WordPress_IPriority::PRIORITY_DEFAULT, 4);
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
            $this->wp->addFilter('wp_kses_allowed_html', array($this, 'filterAllowedHtmlInTaxonomies'));
        } else {
            $this->wp->removeFilter('pre_term_description', 'wp_filter_kses');
        }

        return $this;
    }

    public function setDefaultMetaboxesOrder()
    {
        $boxes = $this->wp->getMetaBoxes('wpt_test', 'side', 'core');
        $boxes = $this->arrayMoveItemAfter($boxes, 'wpt_result_page_options', 'submitdiv');
        $boxes = $this->arrayMoveItemAfter($boxes, 'wpt_test_page_options', 'submitdiv');
        $this->wp->setMetaBoxes($boxes, 'wpt_test', 'side', 'core');
    }

    /**
     * @param array $args
     * @param string $postId
     * @return array
     */
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
        $this->wp->addFilterOnce('get_terms_orderby', array($this, 'filterTermsOrderBy'), WpTesting_WordPress_IPriority::PRIORITY_DEFAULT, 3);
        return $args;
    }

    /**
     * @param array $terms
     * @param integer $objectIds
     * @param array $taxonomies
     * @param array $args
     * @return array
     */
    public function filterForceSortObjectTerms($terms, $objectIds, $taxonomies, $args)
    {
        if (!isset($args['taxonomy']) || !in_array($args['taxonomy'], array('wpt_answer', 'wpt_scale', 'wpt_result'))) {
            return $terms;
        }
        $model = new WpTesting_Model_Taxonomy();
        $terms = $model->sortTermIdsByTermOrder($objectIds, $terms);
        $this->selectedTermsIds[$args['taxonomy']] = $terms;
        $this->wp->addFilterOnce('get_terms_orderby', array($this, 'filterTermsOrderBy'), WpTesting_WordPress_IPriority::PRIORITY_DEFAULT, 3);
        return $terms;
    }

    /**
     * @param string $orderBy
     * @param array $args
     * @param string $taxonomies
     * @return string
     */
    public function filterTermsOrderBy($orderBy, $args, $taxonomies = null)
    {
        if (is_null($taxonomies)) { // Old WP versions workaround
            $this->wp->removeFilter('get_terms_orderby', array($this, 'filterTermsOrderBy'), WpTesting_WordPress_IPriority::PRIORITY_DEFAULT, 3);
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

    /**
     * @param array $allowedTags
     * @return array
     */
    public function filterAllowedHtmlInTaxonomies($allowedTags)
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

    public function renderContentEditorButtons($editorId)
    {
        if ('content' != $editorId) {
            return;
        }
        $this->output('Test/Editor/content-editor-buttons');
    }

    private function addSubmitMiscOptions()
    {
        $options = array(
            'wpt_publish_on_home' => array(
                'default' => '1',
                'title'   => __('Publish on the home page', 'wp-testing'),
            ),
        );

        $isPublishOnHomePossible = ($this->wp->getOption('show_on_front') != 'page');
        if (!$isPublishOnHomePossible) {
            unset($options['wpt_publish_on_home']);
        }

        return $this->addMetaOptions('wpt_test_editor_submit_misc_options', $options);
    }

    public function renderSubmitMiscOptions()
    {
        $this->renderMetaOptions('wpt_test_editor_submit_misc_options');
    }

    private function addTestPageOptions()
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
                'title'   => __('Button caption', 'wp-testing'),
                'type'    => 'text',
                'placeholder' => __('Get Test Results', 'wp-testing'),
            ),
        );

        return $this->addMetaOptions('wpt_test_editor_test_page_options', $options);
    }

    public function renderTestPageOptions()
    {
        $this->renderMetaOptions('wpt_test_editor_test_page_options');
    }

    private function addResultPageOptions()
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

        return $this->addMetaOptions('wpt_test_editor_result_page_options', $options);
    }

    public function renderResultPageOptions()
    {
        $this->renderMetaOptions('wpt_test_editor_result_page_options');
    }

    /**
     * @param WP_Post $item
     */
    public function renderEditQuestionsAnswers($item)
    {
        $this->output('Test/Editor/edit-questions-answers');
    }

    /**
     * @param WP_Post $item
     */
    public function renderEditScores($item)
    {
        $this->output('Test/Editor/edit-scores');
    }

    /**
     * @param WP_Post $item
     */
    public function renderQuickFillScores($item)
    {
        $this->output('Test/Editor/quick-fill-scores');
    }

    /**
     * @param WP_Post $item
     */
    public function renderEditFormulas($item)
    {
        $this->output('Test/Editor/edit-formulas');
    }

    /**
     * @param integer $id
     * @param WP_Post $item
     */
    public function saveTest($id, $item)
    {
        $test = $this->createTest($item);
        $isUpdatable = ($test->getId() && $this->hasMetaOptionsInRequest());

        // Update test only when we have it and at least metaoptions
        if (!$isUpdatable) {
            return;
        }

        try {
            $test->storeAll();
            if ($this->isAjax()) {
                $message = $this->emulateRedirectMessage($test);
                $this->jsonResponse(array(
                    'success' => true,
                    'redirectTo' => $this->wp->getEditPostLink($test->getId(), 'url') . '&message=' . $message,
                ));
            }
        } catch (fValidationException $e) {
            $title = __('Test data not saved', 'wp-testing');
            if ($this->isAjax()) {
                $this->jsonResponse(array(
                    'success' => false,
                    'error' => array(
                        'title' => $title,
                        'content' => $e->getMessage(),
                     )
                ));
            }
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
     * Emulate behavior of redirect_post(), that append message=1 to edit url.
     *
     * @param WpTesting_Model_Test $test
     * @return number
     */
    private function emulateRedirectMessage(WpTesting_Model_Test $test)
    {
        $status = $test->getStatus();
        $isPublish = $this->getRequestValue('publish', 'boolean');

        if (!$isPublish) {
            return ('draft' == $status) ? 10 : 1;
        }

        switch ($status) {
            case 'pending':
                return 8;
            case 'future':
                return 9;
            default:
                return 6;
        }
    }

    /**
     * Do we currently at tests?
     *
     * @param WP_Screen $screen
     * @param string $attribute When comparing by post_type -- we are at any tests screen, by id -- we are at editor
     *
     * @return boolean
     */
    private function isTestScreen($screen, $attribute)
    {
        $id = $this->getRequestValue('post');
        if (is_array($id)) {
            return false;
        }
        if (!empty($screen->$attribute) && $screen->$attribute == 'wpt_test') {
            return true;
        }
        if ($this->isWordPressAlready('3.3')) {
            return false;
        }

        // WP 3.2 workaround
        if ($this->isPost() && $this->getRequestValue('post_type') == 'wpt_test') {
            return true;
        }

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
}
