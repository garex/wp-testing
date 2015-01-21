<?php $lastKey = end(array_keys($options)) ?>
<?php foreach ($options as $key => $option): ?>
<?php $cssClass = str_replace('_', '-', $key) ?>
<?php $cssLast  = ($lastKey == $key) ? ' misc-pub-section-last' : '' ?>
<div class="misc-pub-section <?php echo $cssClass ?> misc-pub-<?php echo $cssClass . $cssLast ?>">
    <label>
        <input type="hidden"   value="0" name="<?php echo $key ?>" />
        <input type="checkbox" value="1" name="<?php echo $key ?>" <?php
            echo ($option['value']) ? 'checked="checked"' : '' ?> /> <?php
            echo $option['title']
    ?></label>
</div>
<?php endforeach ?>
