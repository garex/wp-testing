<div ng-controller="EditScoresController" ng-cloak class="wptApp wpt_scroll">

<table class="widefat" ng-if="!(questions.length && questions[0].answers.length && scales.length)">
<thead>
    <tr class="alternate">
        <td>
            <p class="highlight">
                <?php echo __('No scores to edit. To edit scores you should have questions, answers and selected scales.', 'wp-testing') ?>
            </p>
        </td>
    </tr>
</thead>
</table>

<table class="widefat wpt_scores" ng-if="questions.length && questions[0].answers.length && scales.length">
<colgroup span="2"></colgroup>
<colgroup>
    <col ng-repeat="scale in scales" ng-class="{focused: isFocused(scale)}"></col>
</colgroup>
<colgroup></colgroup>
<tbody ng-repeat-start="question in questions" ng-if="isQuestionSelected(question)">
    <tr class="wpt_scales">
         <th class="bar number wpt_first"><?php echo __('#', 'wp-testing') ?></th>
         <th class="bar"></th>
         <th class="bar wpt_title"
             ng-class="{focused: isFocused(scale)}"
             ng-repeat="scale in scales"
             title="{{ scale.title }}">
             <span class="wpt_meta">∑ {{ scale.sum }}</span>
             <span class="wpt_meta wpt_max" ng-show="scale.maximum != scale.sum">max {{ scale.maximum }}</span>
             <div>{{ scale.title }}</div>
         </th>
         <th class="bar number wpt_last"><?php echo __('#', 'wp-testing') ?></th>
    </tr>
</tbody>
<tbody class="wpt_question" ng-repeat-end ng-mouseenter="isQuestionHovered=true" ng-init="questionIndex = $index; isQuestionHovered = (10 > $index)">
    <tr ng-class="{focused: isFocused(question)}" class="wpt_question_item alternate">
        <th class="wpt_title number wpt_first">{{$index + 1}}</th>
        <td class="wpt_title" colspan="{{ scales.length + 1 }}" title="{{ question.title }}">
            <div class="wpt_question" ng-style="{'max-width': questionMaxWidth }">{{ question.title }}</div>
        </td>
        <th class="wpt_title number wpt_last">{{$index + 1}}</th>
    </tr>
    <tr ng-repeat="answer in question.answers" class="wpt_answer"
        ng-class="{focused: isFocused(answer)}" ng-init="scoreIndexStart = $index * scales.length">
        <td class="wpt_title number wpt_first">{{$index + 1}}</td>
        <td class="wpt_title" title="{{ answer.getTitle() }}"><div>{{ answer.getTitle() }}</div></td>
        <td ng-repeat="score in answer.scoresData()" class="wpt_score"><input
            ng-if="isQuestionHovered"
            type="number" min="-999.999" max="999.999" step="any"
            ng-focus="focus(score)"
            ng-attr-id="wpt_score_value_{{ questionIndex }}_{{ scoreIndexStart + $index }}"
            ng-model="score.value"
            ng-model-options="{getterSetter: true}" /><span ng-if="!isQuestionHovered">{{ score.value() }}</span>
        </td>
        <td class="wpt_title number wpt_last">{{$index + 1}}</td>
    </tr>
</tbody>
</table>

</div>
