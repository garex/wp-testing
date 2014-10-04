<?php $prefix = '' ?>
<table class="widefat wpt_formulas">
    <tr>
        <th class="bar"><?php echo 'Result' ?></th>
        <th class="bar"><?php echo 'Formula' ?></th>
    </tr>
<?php foreach($results as $r => $result): /* @var $result WpTesting_Model_Result */ ?>
    <tr class="wpt_result<?php echo ($r%2) ? ' alternate' : '' ?>">
        <td class="wpt_title">
            <?php echo $result->getTitle() ?>
        </td>
        <td class="wpt_formula">
            <input name="<?php echo $prefix ?>formula_source[<?php echo $r ?>]" value="<?php echo htmlspecialchars('') ?>" />
        </td>
    </tr>
<?php endforeach ?>
</table>

<table class="widefat wpt_formulas_helper">
    <tr>
        <th class="bar"><?php echo 'Variables' ?></th>
        <th class="bar"><?php echo 'Comparisions' ?></th>
    </tr>
    <tr>
        <td>
        <?php foreach($scales as $scale): /* @var $scale WpTesting_Model_Scale */ ?>
            <input type="button" data-source="<?php echo htmlspecialchars($scale->getSlug()) ?>" title="<?php echo 'Scale Variable' ?>" value="<?php echo htmlspecialchars($scale->getTitle()) ?>"/>
        <?php endforeach ?>
        </td>
        <td>
        <?php foreach(explode(', ', '<, >, <=, =>, <>, AND, OR, (, )') as $operator):  ?>
            <input type="button" data-source="<?php echo htmlspecialchars($operator) ?>" title="<?php echo 'Comparision' ?>" value="<?php echo htmlspecialchars($operator) ?>"/>
        <?php endforeach ?>
        </td>
    </tr>
    <tr class="alternate">
        <td colspan="2">
            <div class="howto"><?php echo 'Both numbers and percents allowed. For example "Scale 1" has total 30, then "Scale 1 > 15" and "Scale 1 > 50%" are same.'?></div>
        </td>
    </tr>
</table>
