<?php if ($isWarnOfSettings): ?>
<div class="error">
    <h2>Settings of your server are too low — form values could be cropped!</h2>

    <p>Add this into your php.ini:</p>
    <pre>
<?php foreach(array_keys($memoryWarnSettings) as $key): ?>
    <?php echo $key ?>=5000
<?php endforeach ?>
    </pre>

<?php if($isUnderApache): ?>
    .. or .htaccess

    <pre>
<?php foreach(array_keys($memoryWarnSettings) as $key): ?>
    php_value <?php echo $key ?> 5000
<?php endforeach ?>
    </pre>
<?php endif ?>

    .. and reload (restart) server.
</div>
<?php endif ?>
<?php if($canEditScores): ?>
<div id="wpt_quick_fill_scores" class="hide-if-no-js wp-hidden-children">

    <h4>
        <a href="#wpt-quick-fill-scores" class="toggle"><?php echo __('Quick Fill Scores', 'wp-testing') ?></a>
    </h4>

    <div class="wp-hidden-child">

        <table class="widefat wpt_quick_scores">
        <thead>
            <tr class="bar">
                <th><?php echo __('Scale', 'wp-testing') ?></th>
                <th class="score"><?php echo __('Score', 'wp-testing') ?></th>
                <th><?php echo __('Answer', 'wp-testing') ?></th>
                <th class="questions"><?php echo __('Questions', 'wp-testing') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($scales as $i => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
            <?php foreach($answers as $j => $answer): /* @var $answer WpTesting_Model_Answer */ ?>
                <tr class="quick-score" data-quick-score-class="quick-score-<?php echo $scale->getId() ?>-<?php echo $answer->getId() ?>">
                <?php if (0 == $j): ?>
                    <td rowspan="<?php echo count($answers) ?>"><?php echo $scale->getTitle() ?></td>
                <?php endif ?>
                    <td class="score <?php echo ($j%2) ? 'bar' : 'alternate' ?>">
                        <input type="text"
                            id="wpt_quick_fill_scores_score_<?php echo $i ?>_<?php echo $j ?>"
                            placeholder="<?php echo htmlspecialchars($scale->getAbbr()) ?>" />
                    </td>
                    <td class="answer <?php echo ($j%2) ? 'bar' : 'alternate' ?>"><?php echo $answer->getTitle() ?></td>
                    <td class="questions <?php echo ($j%2) ? 'bar' : 'alternate' ?>">
                        <input type="text"
                            id="wpt_quick_fill_scores_questions_<?php echo $i ?>_<?php echo $j ?>"
                            placeholder="<?php echo htmlspecialchars($scale->getAbbr()) ?>" />
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endforeach ?>
        </tbody>
        </table>

        <input type="button" class="button" value="<?php echo __('Quick Fill Scores', 'wp-testing') ?>" />
    </div>
</div>
<?php endif ?>
<table class="widefat wpt_questions">
<?php if(count($scales)): ?>
    <tr>
        <th></th>
    <?php foreach($scales as $i => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
        <th class="wpt_scale <?php echo ($i%2) ? '' : 'alternate' ?>">
            <?php echo $scale->getTitle() ?>
            <span class="alignright wp-ui-text-icon">∑ <?php echo $scale->getSum() ?></span>
        </th>
    <?php endforeach ?>
    </tr>
<?php endif ?>
<?php $fullColspan = 1 + max(count($scales), 1) ?>
<?php if(!count($questions)): ?>
    <tr class="alternate">
        <td colspan="<?php echo $fullColspan ?>">
            <p class="highlight">
                <?php echo __('No questions to edit. Add new questions and then they will appear here.', 'wp-testing') ?>
            </p>
        </td>
    </tr>
<?php elseif(!$canEditScores): ?>
    <tr class="alternate">
        <td colspan="<?php echo $fullColspan ?>">
            <p class="highlight">
                <?php echo __('No scores to edit. To edit scores you must have both answers and scales selected.', 'wp-testing') ?>
            </p>
        </td>
    </tr>
<?php endif ?>
<?php foreach($questions as $q => $question): /* @var $question WpTesting_Model_Question */ ?>
    <tr class="wpt_question">
        <th class="wpt_number bar">
            <?php echo $q+1 ?>
        </th>
        <td class="wpt_title bar" colspan="<?php echo count($scales) ?>">
            <input type="text"
                name='wpt_question_title[<?php echo json_encode(array(
                    'i'  => $q,
                    'id' => $question->getId(),
                ))  ?>]'
                id="wpt_question_title_<?php echo $q ?>"
                value="<?php echo htmlspecialchars($question->getTitle()) ?>" />
        </td>
    </tr>
    <?php $scoreIndex = 0 ?>
    <?php foreach($question->getAnswers() as $a => $answer): /* @var $answer WpTesting_Model_Answer */ ?>
        <tr>
            <td class="wpt_answer subtitle"><?php echo $answer->getTitle() ?></td>
        <?php foreach($scales as $s => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
            <?php $score = $question->getScoreByAnswerAndScale($answer, $scale) ?>
            <td class="wpt_scale quick-score <?php echo ($s%2) ? '' : 'alternate' ?>">
                <input type="text"
                    placeholder="<?php echo htmlspecialchars($scale->getAbbr()) ?>"
                    name='wpt_score_value[<?php echo json_encode(array(
                        'i'         => $q,
                        'j'         => $scoreIndex,
                        'answer_id' => $answer->getId(),
                        'scale_id'  => $scale->getId(),
                    ))  ?>]'
                    data-question-number="<?php echo $q+1 ?>"
                    class="quick-score-<?php echo $scale->getId() ?>-<?php echo $answer->getId() ?> question-<?php echo $q+1 ?>"
                    id="wpt_score_value_<?php echo $q ?>_<?php echo $scoreIndex ?>"
                    value="<?php echo $score->getValueWithoutZeros() ?>"
                    title="<?php echo htmlspecialchars($scale->getTitle() . ', ' . $answer->getTitle()) ?>" />
            </td>
            <?php $scoreIndex++ ?>
        <?php endforeach ?>
        </tr>
    <?php endforeach ?>
<?php endforeach ?>

</table>