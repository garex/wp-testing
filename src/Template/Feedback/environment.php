<?php
/* @var $wp WpTesting_WordPressFacade */
/* @var $values array */
/* @var $parameters WpTesting_Model_IEnvironment[] */
?>
<div class="wrap">
<h1><?php echo __('System information', 'wp-testing')?></h1>

<form id="environment" method="post">
<?php foreach ($values as $key => $value): ?>
<?php if (!is_array($value)): ?>
    <input type="hidden" name="<?php echo $key ?>" value="<?php echo htmlentities($value) ?>" />
<?php else: ?>
<?php foreach ($value as $subkey => $subvalue): ?>
    <input type="hidden" name="<?php echo $key ?>[<?php echo $subkey ?>]" value="<?php echo htmlentities($subvalue) ?>" />
<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>

<table class="form-table">
<tbody>
<?php foreach ($parameters as $parameter): ?>
<tr>
    <th><?php echo $parameter->label() ?></th>
    <td>
        <label for="Parameter<?php echo md5($parameter->label()) ?>">
            <input name="parameters[]" class="parameter" id="Parameter<?php echo md5($parameter->label()) ?>" type="checkbox" value="<?php echo htmlentities('<code>' . $parameter->label(). '</code> ' . $parameter->text()) ?>" /><?php
            echo $parameter->text()
          ?></label>
    </td>
</tr>
<?php endforeach ?>
<tr>
    <th>
        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Next', 'wp-testing') ?>" />
    </th>
    <td>
        <label for="SelectAll"><input id="SelectAll" type="checkbox" data-select-all=".parameter" /><?php
            echo $wp->translate('Select All')
      ?></label>
    </td>
</tr>
</tbody>
</table>

</form>

</div>

<script>
jQuery(document).ready(function($) {
    $('[data-select-all]').change(function() {
        var selector = $(this).data('selectAll');

        $(selector).prop('checked', $(this).prop('checked'));
    });
});
</script>

