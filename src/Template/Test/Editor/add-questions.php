<div id="wpt_quick_fill_questions" class="hide-if-no-js wp-hidden-children">

<h4>
    <a href="#wpt-quick-fill-questions" class="toggle"><?php echo __('Quick Fill From Text', 'wp-testing') ?></a>
</h4>

<div class="wp-hidden-child">
<textarea rows="20" cols="50" placeholder="<?php echo __('Paste here your questions and they will fill fields below. Numbers and other indexes will be stripped automatically.', 'wp-testing') ?>"></textarea>
<input type="button" class="button" value="<?php echo __('Quick Fill From Text', 'wp-testing') ?>" />
</div>

</div>

<table class="widefat wpt_questions" data-start-from="<?php echo $startFrom ?>">
<?php foreach(range(0, $addNewCount) as $i): ?>
    <tr class="wpt_question <?php echo ($i%2) ? 'alternate' :'bar' ?>">
        <th class="wpt_number">
            *
        </th>
        <td class="wpt_title">
            <input type="text" name='wpt_question_title[<?php echo json_encode(array(
                'i'  => $startFrom + $i,
                'id' => '',
            ))  ?>]' id="wpt_question_title_<?php echo $startFrom + $i ?>" />
        </td>
    </tr>
<?php endforeach ?>
</table>
