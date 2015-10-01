<?php $optionsKeys = array_keys($options) ?>
<?php $lastKey     = end($optionsKeys) ?>
<?php foreach ($options as $key => $option): ?>
<?php $cssClass = str_replace('_', '-', $key) ?>
<?php $cssLast  = ($lastKey == $key) ? ' misc-pub-section-last' : '' ?>
<?php if ($option['break']): ?>
    <hr/>
<?php endif ?>
<div class="misc-pub-section <?php echo $cssClass ?> misc-pub-<?php echo $cssClass . $cssLast ?>">
<?php if ('checkbox' == $option['type']): ?>
    <label>
        <input type="hidden"   value="0" name="<?php echo $key ?>" />
        <input type="checkbox" value="1" name="<?php echo $key ?>" <?php
            echo ($option['value']) ? 'checked="checked"' : '' ?> /> <?php
            echo $option['title']
    ?></label>
<?php elseif ('text' == $option['type']): ?>
    <label><?php echo $option['title'] ?></label>
    <input type="text" name="<?php echo $key ?>" value="<?php echo $option['value'] ?>" placeholder="<?php echo $option['placeholder'] ?>" />
<?php elseif ('select' == $option['type']): ?>
    <label><?php echo $option['title'] ?></label>
    <select name="<?php echo $key ?>">
    <?php foreach ($option['values'] as $valueKey => $valueTitle): ?>
        <option value="<?php echo $valueKey ?>" <?php if ($valueKey == $option['value']): ?>selected="selected"<?php endif ?>><?php echo $valueTitle ?></option>
    <?php endforeach ?>
    </select>
<?php endif ?>
</div>
<?php endforeach ?>
