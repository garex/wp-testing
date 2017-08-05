<div ng-controller="EditQuestionTreeController" ng-cloak class="wptApp wpt_quick_scores">

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

<table class="widefat wpt_quick_scores" ng-if="questions.length && questions[0].answers.length && scales.length">
    <thead>
        <tr class="bar">
            <th><?php echo __('Scale', 'wp-testing') ?></th>
            <th class="score"><?php echo __('Score', 'wp-testing') ?></th>
            <th><?php echo __('Answer', 'wp-testing') ?></th>
            <th class="questions"><?php echo __('Questions', 'wp-testing') ?></th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="entry in tree.entries()" ng-class-even="'alternate'" class="entry_index_{{ $index }}">
            <td class="scale">{{ entry.key.scaleTitle }}</td>
            <td class="score">{{ entry.key.scoreValue }}</td>
            <td class="answer">{{ entry.key.answerTitle }}</td>
            <td class="questions"><input
                type="text"
                ng-model="entry.value.joinedKeys"
                pattern="[0-9, ]*"
                ng-change="tree.onValueChanged(entry)" /></td>
        </tr>
        <tr class="bar" ng-if="tree.isTall()">
            <th><?php echo __('Scale', 'wp-testing') ?></th>
            <th class="score"><?php echo __('Score', 'wp-testing') ?></th>
            <th><?php echo __('Answer', 'wp-testing') ?></th>
            <th class="questions"></th>
        </tr>
        <tr class="wpt_add_new_combination">
            <th class="scale"><select ng-model="newScaleIndex">
                <option value=""><?php echo $this->wp->translate('&mdash; Select &mdash;') ?></option>
                <option ng-repeat="scale in scales" value="{{ $index }}">{{ scale.title }}</option>
            </select></th>
            <td class="score"><input ng-model="newScore" type="number" min="-999.999" max="999.999" step="any" /></td>
            <td class="answer"><input ng-model="newAnswer" type="number" min="1" max="1000" step="1" /></td>
            <td><button ng-click="tree.addNew(scales[newScaleIndex], newScore, newAnswer)" type="button" class="button"><?php echo __('Add new combination', 'wp-testing') ?></button></td>
        </tr>
    </tbody>
</table>
</div>
