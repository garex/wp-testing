<?php $number = 1; ?>
<table>
<?php foreach($questions as $question): /* @var $question WpTesting_Model_Question */ ?>
    <tr class="wpt_question">
        <th class="wpt_number">
            <?php echo $number++ ?>
            <input type="hidden" name="<?php echo $prefix ?>question_id[]" value="<?php echo $question->getId() ?>" />
        </th>
        <td class="wpt_title">
            <input name="<?php echo $prefix ?>question_title[]" value="<?php echo $question->getTitle() ?>" />
        </td>
    </tr>
<?php endforeach ?>
<?php foreach(range($number, $number - 1 + $addNewCount) as $number): ?>
    <tr class="wpt_question">
        <th class="wpt_number">
            *
            <input type="hidden" name="<?php echo $prefix ?>question_id[]" />
        </th>
        <td class="wpt_title">
            <input name="<?php echo $prefix ?>question_title[]" />
        </td>
    </tr>
<?php endforeach ?>
</table>