<div ng-controller="EditFormulasController" ng-cloak class="wptApp">

<table class="widefat wpt_formulas" wpt-formulas>
    <tr>
        <th class="wpt_number bar"><?php echo __('#', 'wp-testing') ?></th>
        <th class="bar"><?php echo __('Formula', 'wp-testing') ?></th>
        <th class="bar"><?php echo __('Result', 'wp-testing') ?></th>
    </tr>
    <tr ng-repeat-start="result in results" class="wpt_result" ng-if="isActive($index)" ng-class-even="'alternate'">
        <th class="wpt_number bar"></th>
        <td colspan="2">
            <wpt-formula-toolbar></wpt-formula-toolbar>
        </td>
    </tr>
    <tr ng-repeat-end class="wpt_result" ng-class-even="'alternate'">
        <th class="wpt_number bar">{{$index + 1}}</th>
        <td class="wpt_formula">
            <input type="text"
                wpt-formula-input
                caret-aware
                ng-attr-id="wpt_formula_source_{{$index}}"
                placeholder="<?php echo __('Formula', 'wp-testing') ?>"
                ng-model="result.formula.source"/>
        </td>
        <td class="wpt_title" >
            <a target="_blank"
                tabindex="-1"
                title="{{result.tooltip}}"
                href="{{result.editLink}}">{{result.title}}</a>
        </td>
    </tr>
    <tr class="alternate" ng-if="!results.length">
        <td colspan="3">
            <p class="highlight">
                <?php echo __('No formulas to edit. To edit formulas you must have results selected.', 'wp-testing') ?>
            </p>
            <wpt-formula-toolbar ng-init="$index = 0" disabled="true"></wpt-formula-toolbar>
            <input type="hidden" wpt-formula-input />
        </td>
    </tr>
</table>
<input type="hidden" name="wpt_formulas_json" value="{{ results.formulas | json:0 }}" />
</div>

<div id="wptFormulaToolbarDirectiveTemplate" class="wpt_template">
<div class="wpt_formulas_toolbars">
    <div class="howto alternate"><?php
    /* translators: "scale-bla" should not be translated */
    echo __('Both numbers and percents allowed. For example "scale-bla" has total 30, then "scale-bla > 15" and "scale-bla > 50%" are same.', 'wp-testing');
    ?></div>

    <div class="wpt_formulas_toolbar">
        <?php echo __('Comparisions', 'wp-testing') ?>
        <input type="button"
            class="button"
            ng-disabled="isDisabled"
            ng-repeat="action in comparisions"
            ng-click="runAction(action)"
            title="{{ action.getTooltip() }}"
            value="{{ action.getLabel() }}"/>

        <?php echo __('Operators', 'wp-testing') ?>
        <input type="button"
            class="button"
            ng-disabled="isDisabled"
            ng-repeat="action in operators"
            ng-click="runAction(action)"
            title="{{ action.getTooltip() }}"
            value="{{ action.getLabel() }}"/>
    </div>

    <div class="wpt_formulas_toolbar" ng-if="hasVariables()">
        <?php echo __('Variables', 'wp-testing') ?>
        <div type="button"
            ng-disabled="isDisabled"
            ng-if="isQuestionAnswerActive()"
            class="question-answer button"
            ng-click="runAction(questionAnswerAction)">
        <?php echo sprintf(__('Question %1$s answer %2$s', 'wp-testing'),
            '<input ng-disabled="isDisabled" max="{{maxQuestionsCount()}}" min="1" ng-click="$event.stopPropagation()" ng-model="questionAnswerAction.question" type="number" />',
            '<input ng-disabled="isDisabled" max="{{maxAnswersCount()}}" min="1" ng-click="$event.stopPropagation()" ng-model="questionAnswerAction.answer" type="number" />') ?>
        </div><input type="button"
            class="button"
            ng-disabled="isDisabled || !action.getLabel()"
            ng-repeat="action in variables | filter:isVariableAvailable"
            ng-click="runAction(action)"
            title="{{ action.getTooltip() }}"
            value="{{ action.getLabel() }}"/>
    </div>

    <p class="highlight" ng-if="!hasVariables()">
        <?php echo __('No variables for formulas available. To use variables you must have scales selected or have at least one question and answer.', 'wp-testing') ?>
    </p>
</div>
</div>
