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
            <?php foreach($answers as $j => $answer): /* @var $answer WpTesting_Model_GlobalAnswer */ ?>
                <tr class="quick-score" data-quick-score-class="quick-score-<?php echo $scale->getId() ?>-<?php echo $answer->getId() ?>">
                <?php if (0 == $j): ?>
                    <td rowspan="<?php echo count($answers) ?>"><?php echo $scale->getTitleOnce() ?></td>
                <?php endif ?>
                    <td class="score <?php echo ($j%2) ? 'bar' : 'alternate' ?>">
                        <input type="text"
                            id="wpt_quick_fill_scores_score_<?php echo $i ?>_<?php echo $j ?>"
                            placeholder="<?php echo htmlspecialchars($scale->getAbbrOnce()) ?>" />
                    </td>
                    <td class="answer <?php echo ($j%2) ? 'bar' : 'alternate' ?>"><?php echo $answer->getTitleOnce() ?></td>
                    <td class="questions <?php echo ($j%2) ? 'bar' : 'alternate' ?>">
                        <input type="text"
                            id="wpt_quick_fill_scores_questions_<?php echo $i ?>_<?php echo $j ?>"
                            placeholder="<?php echo htmlspecialchars($scale->getAbbrOnce()) ?>" />
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
            <?php echo $scale->getTitleOnce() ?>
            <span class="alignright wp-ui-text-icon"><?php echo $scale->getAggregatesTitle() ?></span>
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
                name="wpt_question_title[<?php echo $question->encodeSafeUriValue(array(
                    'q'  => $q,
                    'id' => $question->getId(),
                )) ?>]"
                id="wpt_question_title_<?php echo $q ?>"
                value="<?php echo htmlspecialchars($question->getTitleOnce()) ?>" />
        </td>
    </tr>
    <?php $scoreIndex = 0 ?>
    <?php foreach($question->buildAnswers() as $a => $answer): /* @var $answer WpTesting_Model_Answer */ ?>
        <tr>
            <td class="wpt_answer subtitle answer-<?php echo $answer->getIndividuality() ?>">
                <input type="text"
                    placeholder="<?php echo htmlspecialchars($answer->getGlobalTitle()) ?>"
                    name="wpt_answer_title[<?php echo $answer->encodeSafeUriValue(array(
                        'q'  => $q,
                        'a'  => $a,
                        'id' => $answer->getId(),
                    )) ?>]"
                    id="wpt_answer_title_<?php echo $q ?>_<?php echo $a ?>"
                    value="<?php echo htmlspecialchars($answer->getIndividualTitle()) ?>"
                    title="<?php echo htmlspecialchars($answer->getGlobalTitle()) ?>" />
            </td>
        <?php foreach($scales as $s => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
            <?php $score = $answer->getScoreByScale($scale) ?>
            <td class="wpt_scale quick-score <?php echo ($s%2) ? '' : 'alternate' ?>">
                <input type="text"
                    placeholder="<?php echo htmlspecialchars($scale->getAbbrOnce()) ?>"
                    name="wpt_score_value[<?php echo $scale->encodeSafeUriValue(array(
                        'q'         => $q,
                        'a'         => $a,
                        's'         => $scoreIndex,
                        'answer_id' => $answer->getId(),
                        'scale_id'  => $scale->getId(),
                    )) ?>]"
                    data-question-number="<?php echo $q+1 ?>"
                    class="quick-score-<?php echo $scale->getId() ?>-<?php echo $answer->getGlobalAnswerId() ?> question-<?php echo $q+1 ?>"
                    id="wpt_score_value_<?php echo $q ?>_<?php echo $scoreIndex ?>"
                    value="<?php echo $score->getValueWithoutZeros() ?>"
                    title="<?php echo htmlspecialchars($scale->getTitleOnce() . ', ' . $answer->getTitleOnce()) ?>" />
            </td>
            <?php $scoreIndex++ ?>
        <?php endforeach ?>
        </tr>
    <?php endforeach ?>
        <tr><td colspan="<?php echo $fullColspan ?>" class="wpt-add-individual-answers">
            <p>
                <a href="#wpt-add-individual-answers-to-question-<?php echo $q ?>" class="toggle"><?php echo __('Add Individual Answers', 'wp-testing') ?></a>
                <span class="wp-hidden-child howto"><?php echo sprintf(__('— unique to each question. If you have same answers to all test questions, use the %s', 'wp-testing'), '<a href="#wpt_answerdiv">' . __('Test Answers', 'wp-testing') . '</a>') ?></span>
            </p>
            <div class="wp-hidden-child">
                <textarea
                    placeholder="<?php echo __('Add here your individual answers as text; after test saving they will be extracted and created. Numbers and other indexes will be stripped automatically.', 'wp-testing') ?>"
                    name="wpt_question_individual_answers[<?php echo $question->encodeSafeUriValue(array(
                        'q'  => $q,
                        'id' => $question->getId(),
                    )) ?>]"
                    id="wpt-add-individual-answers-to-question-<?php echo $q ?>"
                    rows="5" cols="120"></textarea>
            </div>
        </td></tr>
<?php endforeach ?>

</table>