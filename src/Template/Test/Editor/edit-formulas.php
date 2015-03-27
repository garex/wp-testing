<table class="widefat wpt_formulas">
    <tr>
        <th class="bar"><?php echo __('Formula', 'wp-testing') ?></th>
        <th class="bar"><?php echo __('Result', 'wp-testing') ?></th>
    </tr>
<?php foreach($results as $r => $result): /* @var $result WpTesting_Model_Result */ ?>
    <tr class="wpt_result<?php echo ($r%2) ? ' alternate' : '' ?>">
        <td class="wpt_formula">
            <?php $formula = $result->getFormula() ?>
            <input type="text"
                name='wpt_formula_source[<?php echo json_encode(array(
                    'i'          => $r,
                    'formula_id' => $formula->getId(),
                    'result_id'  => $formula->getResultId(),
                ))  ?>]'
                id="wpt_formula_source_<?php echo $r ?>"
                value="<?php echo htmlspecialchars($formula->getSource()) ?>" />
        </td>
        <td class="wpt_title">
            <?php echo $result->getTitle() ?>
        </td>
    </tr>
<?php endforeach ?>
<?php if (!count($results)): ?>
    <tr class="alternate">
        <td colspan="2">
            <p class="highlight">
                <?php echo __('No formulas to edit. To edit formulas you must have results selected.', 'wp-testing') ?>
            </p>
        </td>
    </tr>
<?php endif ?>
</table>

<table class="widefat wpt_formulas_helper">
    <tr>
        <th class="bar"><?php echo __('Variables', 'wp-testing') ?></th>
        <th class="bar"><?php echo __('Comparisions', 'wp-testing') ?></th>
        <th class="bar"><?php echo __('Operators', 'wp-testing') ?></th>
    </tr>
    <tr>
        <td>
        <?php foreach($variables as $variable): /* @var $variable WpTesting_Model_FormulaVariable */ ?>
            <input type="button" data-source="<?php echo htmlspecialchars($variable->getSource()) ?>" title="<?php echo $variable->getTypeLabel() ?>" value="<?php echo htmlspecialchars($variable->getTitle()) ?>"/>
        <?php endforeach ?>
        </td>
        <td class="operators">
        <?php foreach(explode(', ', '<, >, <=, =>, <>, AND, OR, (, )') as $operator):  ?>
            <input type="button" data-source="<?php echo htmlspecialchars($operator) ?>" title="<?php echo __('Comparision', 'wp-testing') ?>" value="<?php echo htmlspecialchars($operator) ?>"/>
        <?php endforeach ?>
        </td>
        <td class="operators">
        <?php foreach(explode(', ', '+, -, *, /') as $operator):  ?>
            <input type="button" data-source="<?php echo htmlspecialchars($operator) ?>" title="<?php echo __('Operator', 'wp-testing') ?>" value="<?php echo htmlspecialchars($operator) ?>"/>
        <?php endforeach ?>
        </td>
    </tr>
<?php if (!count($variables)): ?>
    <tr class="alternate">
        <td colspan="3">
            <p class="highlight">
                <?php echo __('No variables for formulas available. To use variables you must have scales selected.', 'wp-testing') ?>
            </p>
        </td>
    </tr>
<?php endif ?>
    <tr class="alternate">
        <td colspan="3">
            <div class="howto"><?php
            /* translators: "scale-bla" should not ne translated */
            echo __('Both numbers and percents allowed. For example "scale-bla" has total 30, then "scale-bla > 15" and "scale-bla > 50%" are same.', 'wp-testing');
            ?></div>
        </td>
    </tr>
</table>
