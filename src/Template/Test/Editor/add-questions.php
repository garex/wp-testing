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
        </th>
        <td class="wpt_title" colspan="<?php echo count($scales) ?>">
            <input type="text" name='wpt_question_title[<?php echo json_encode(array(
                'i'  => $startFrom + $i,
                'id' => '',
            ))  ?>]' id="wpt_question_title_<?php echo $startFrom + $i ?>" />
        </td>
    </tr>
<?php endforeach ?>
</table>