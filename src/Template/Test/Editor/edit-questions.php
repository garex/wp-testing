<?php if ($isWarnOfSettings): ?>
<div class="error">
    <h2>Settings of your server are too low — form values could be cropped!</h2>

    <p>Add this into your php.ini:</p>
    <pre>
    max_input_vars=5000
    suhosin.get.max_vars=5000
    suhosin.post.max_vars=5000
    suhosin.request.max_vars=5000</pre>

    .. or .htaccess

    <pre>
    php_value max_input_vars 5000
    php_value suhosin.get.max_vars 5000
    php_value suhosin.post.max_vars 5000
    php_value suhosin.request.max_vars 5000</pre>

    .. and reload (restart) server.
</div>
<?php endif ?>
<table class="widefat wpt_questions">
    <tr>
        <th></th>
    <?php foreach($scales as $i => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
        <th class="wpt_scale <?php echo ($i%2) ? '' : 'alternate' ?>">
            <?php echo $scale->getTitle() ?>
            <span class="alignright wp-ui-text-icon">∑ <?php echo $scale->getMaximum() ?></span>
        </th>
    <?php endforeach ?>
    </tr>
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
                value="<?php echo htmlspecialchars($question->getTitle()) ?>" />
        </td>
    </tr>
    <?php $scoreIndex = 0 ?>
    <?php foreach($question->getAnswers() as $a => $answer): /* @var $answer WpTesting_Model_Answer */ ?>
        <tr>
            <td class="wpt_answer subtitle"><?php echo $answer->getTitle() ?></td>
        <?php foreach($scales as $s => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
            <?php $score = $question->getScoreByAnswerAndScale($answer, $scale) ?>
            <td class="wpt_scale <?php echo ($s%2) ? '' : 'alternate' ?>">
                <input type="text"
                    placeholder="<?php echo htmlspecialchars($scale->getAbbr()) ?>"
                    name='wpt_score_value[<?php echo json_encode(array(
                        'i'         => $q,
                        'j'         => $scoreIndex++,
                        'answer_id' => $answer->getId(),
                        'scale_id'  => $scale->getId(),
                    ))  ?>]'
                    value="<?php echo $score->getValueWithoutZeros() ?>"
                    title="<?php echo htmlspecialchars($scale->getTitle() . ', ' . $answer->getTitle()) ?>" />
            </td>
        <?php endforeach ?>
        </tr>
    <?php endforeach ?>
<?php endforeach ?>
</table>