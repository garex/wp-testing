<table class="widefat wpt_questions">
    <tr>
        <th></th>
    <?php foreach($scales as $i => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
        <th class="wpt_scale <?php echo ($i%2) ? '' : 'alternate' ?>">
            <?php echo $scale->getTitle() ?>
        </th>
    <?php endforeach ?>
    </tr>
<?php foreach($questions as $q => $question): /* @var $question WpTesting_Model_Question */ ?>
    <tr class="wpt_question">
        <th class="wpt_number bar">
            <?php echo $q+1 ?>
            <input type="hidden" name="<?php echo $prefix ?>question_id[]" value="<?php echo $question->getId() ?>" />
        </th>
        <td class="wpt_title bar" colspan="<?php echo count($scales) ?>">
            <input name="<?php echo $prefix ?>question_title[]" value="<?php echo htmlspecialchars($question->getTitle()) ?>" />
        </td>
    </tr>
    <?php foreach($question->getAnswers() as $answer): /* @var $answer WpTesting_Model_Answer */ ?>
        <tr>
            <td class="wpt_answer subtitle"><?php echo $answer->getTitle() ?></td>
        <?php foreach($scales as $s => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
            <?php $score = $question->getScoreByAnswerAndScale($answer, $scale) ?>
            <?php $scorePrefix  = $prefix . 'wp_testing_model_score::' ?>
            <?php $scorePostfix = '[' . $q . '][]' ?>
            <td class="wpt_scale <?php echo ($s%2) ? '' : 'alternate' ?>">
                <input type="hidden"
                    name="<?php echo $scorePrefix ?>answer_id<?php echo $scorePostfix ?>"
                    value="<?php echo $answer->getId() ?>" />
                <input type="hidden"
                    name="<?php echo $scorePrefix ?>scale_id<?php echo $scorePostfix ?>"
                    value="<?php echo $scale->getId() ?>" />
                <input placeholder="<?php echo htmlspecialchars($scale->getAbbr()) ?>"
                    name="<?php echo $scorePrefix ?>score_value<?php echo $scorePostfix ?>"
                    value="<?php echo $score->getValueWithoutZeros() ?>"
                    title="<?php echo htmlspecialchars($scale->getTitle() . ', ' . $answer->getTitle()) ?>" />
            </td>
        <?php endforeach ?>
        </tr>
    <?php endforeach ?>
<?php endforeach ?>
</table>