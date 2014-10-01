<table class="widefat wpt_questions">
    <tr>
        <th></th>
    <?php foreach($scales as $i => $scale): /* @var $scale WpTesting_Model_Scale */ ?>
        <th class="wpt_scale <?php echo ($i%2) ? '' : 'alternate' ?>">
            <?php echo $scale->getTitle() ?>
        </th>
    <?php endforeach ?>
    </tr>
<?php foreach(range(0, $addNewCount) as $i): ?>
    <tr class="wpt_question <?php echo ($i%2) ? 'alternate' :'bar' ?>">
        <th class="wpt_number">
            *
            <input type="hidden" name="<?php echo $prefix ?>question_id[<?php echo $startFrom + $i ?>]" />
        </th>
        <td class="wpt_title" colspan="<?php echo count($scales) ?>">
            <input name="<?php echo $prefix ?>question_title[<?php echo $startFrom + $i ?>]" />
        </td>
    </tr>
<?php endforeach ?>
</table>