<table class="widefat wpt_questions">
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