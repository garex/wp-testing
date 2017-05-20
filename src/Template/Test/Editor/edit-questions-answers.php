<div ng-controller="EditQuestionsAnswersController" ng-cloak class="wptApp">
<table class="widefat wpt_questions">
<thead ng-if="!questions.length">
    <tr class="alternate">
        <td colspan="5">
            <p class="highlight">
                <?php echo __('No questions to edit. Add new questions and then they will appear here.', 'wp-testing') ?>
            </p>
        </td>
    </tr>
</thead>
<tbody ng-repeat="question in questions" class="wpt_question" ng-init="questionIndex = $index">
    <tr>
        <th class="wpt_number bar">{{$index + 1}}</th>
        <td class="wpt_title bar" colspan="3">
            <input type="text"
                ng-attr-id="wpt_question_title_{{$index}}"
                ng-model="question.title"
                wpt-set-focus="$last && !question.id" />
        </td>
        <td class="actions bar">
            <button ng-click="questions.remove($index)" title="<?php echo __('Remove Question', 'wp-testing') ?>" type="button"
                class="notice-dismiss"></button>
        </td>
    </tr>
    <tr ng-repeat="answer in question.answers" class="wpt_answer">
        <td></td>

        <td class="wpt_number ">{{$index + 1}}</td>
        <td>
            <input type="text"
                placeholder="{{ answer.getTitle() }}"
                title="{{ answer.getTitle() }}"
                ng-attr-id="wpt_answer_title_{{ questionIndex }}_{{ $index }}"
                ng-model="answer.title"
                wpt-set-focus="$last && !answer.id && !answer.global_answer_id" />
        </td>
        <td class="actions">
            <button ng-hide="answer.global_answer_id" ng-click="question.answers.remove($index)" title="<?php echo __('Remove Answer', 'wp-testing') ?>" type="button"
                class="notice-dismiss"></button>
        </td>

        <td></td>
    </tr>
    <tr>
        <td></td>

        <td colspan="5" class="wpt_individual_action">
            <button ng-click="question.answers.addNew()" type="button" class="button"><?php echo __('Add Individual Answer', 'wp-testing') ?></button>
            <span class="note"><?php echo sprintf(__('— unique to each question. If you have same answers to all test questions, use the %s', 'wp-testing'), '<a href="#wpt_answerdiv" ng-click="highlight(\'#wpt_answerdiv\')">' . __('Test Answers', 'wp-testing') . '</a>') ?></span>
        </td>
    </tr>
</tbody>
<tfoot ng-controller="EditQuickFillController">
    <tr ng-show="visible">
        <td colspan="6" class="quick-fill">
            <textarea ng-model="content" rows="20" cols="50" placeholder="<?php echo __('Paste here your questions and they will fill fields below. Numbers and other indexes will be stripped automatically.', 'wp-testing') ?>"></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <button ng-hide="visible" ng-click="questions.addNew()" id="wpt_question_add" type="button" class="button"><?php echo __('Add Question', 'wp-testing') ?></button>
            <a      ng-hide="visible" ng-click="toggle()" href="" class="toggle button"><?php echo __('Quick Fill From Text', 'wp-testing') ?></a>
            <button ng-show="visible" ng-click="process(questions)" type="button" class="button"><?php echo __('Quick Fill From Text', 'wp-testing') ?></button>
            <a      ng-show="visible" ng-click="toggle()" href="" class="toggle button"><?php echo $this->wp->translate('Cancel') ?></a>
        </td>
    </tr>
</tfoot>
</table>
<input type="hidden" name="wpt_questions_answers_json" value="{{ questions | json:0 }}" />
</div>
