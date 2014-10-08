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
            <?php $formula = $result->getFormula() ?>
            <input name="<?php echo $prefix ?>test_id[<?php echo $r ?>]"        value="<?php echo $formula->getTestId() ?>" type="hidden" />
            <input name="<?php echo $prefix ?>formula_id[<?php echo $r ?>]"     value="<?php echo $formula->getId() ?>" type="hidden" />
            <input name="<?php echo $prefix ?>result_id[<?php echo $r ?>]"      value="<?php echo $formula->getResultId() ?>" type="hidden" />
            <input name="<?php echo $prefix ?>formula_source[<?php echo $r ?>]" value="<?php echo htmlspecialchars($formula->getSource()) ?>" type="text" />
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
        <?php foreach($variables as $variable): /* @var $variable WpTesting_Model_FormulaVariable */ ?>
            <input type="button" data-source="<?php echo htmlspecialchars($variable->getSource()) ?>" title="<?php echo $variable->getTypeLabel() ?>" value="<?php echo htmlspecialchars($variable->getTitle()) ?>"/>
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
