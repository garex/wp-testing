<?php $number = 1; ?>
<table class="widefat">
    <tr>
        <th></th>
    <?php foreach($scales as $i => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
        <th class="wpt_scale <?php echo ($i%2) ? '' : 'alternate' ?>">
            <?php echo $scale->getTitle() ?>
        </th>
    <?php endforeach ?>
    </tr>
<?php foreach($questions as $question): /* @var $question WpTesting_Model_Question */ ?>
    <tr class="wpt_question">
        <th class="wpt_number bar">
            <?php echo $number++ ?>
            <input type="hidden" name="<?php echo $prefix ?>question_id[]" value="<?php echo $question->getId() ?>" />
        </th>
        <td class="wpt_title bar" colspan="<?php echo count($scales) ?>">
            <input name="<?php echo $prefix ?>question_title[]" value="<?php echo htmlspecialchars($question->getTitle()) ?>" />
        </td>
    </tr>
    <?php foreach($answers as $answer): /* @var $answer WpTesting_Model_Answer */ ?>
        <tr>
            <td class="wpt_answer subtitle"><?php echo $answer->getTitle() ?></td>
        <?php foreach($scales as $i => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
            <td class="wpt_scale <?php echo ($i%2) ? '' :'alternate' ?>">
                <input placeholder="<?php echo htmlspecialchars($scale->getAbbr()) ?>"
                    title="<?php echo htmlspecialchars($scale->getTitle() . ', ' . $answer->getTitle()) ?>" />
            </td>
        <?php endforeach ?>
        </tr>
    <?php endforeach ?>
<?php endforeach ?>
<?php foreach(range($number, $number - 1 + $addNewCount) as $i => $number): ?>
    <tr class="wpt_question <?php echo ($i%2) ? 'alternate' :'bar' ?>">
        <th class="wpt_number">
            *
            <input type="hidden" name="<?php echo $prefix ?>question_id[]" />
        </th>
        <td class="wpt_title" colspan="<?php echo count($scales) ?>">
            <input name="<?php echo $prefix ?>question_title[]" />
        </td>
    </tr>
<?php endforeach ?>
</table>