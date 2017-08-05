<?php
/* @var $asap int */
/* @var $text string */
?>
<div class="wrap asap-wrap asap-<?php echo $asap ?>">
<h1 class="asap-0"><?php echo __('Get the support', 'wp-testing')?></h1>
<h1 class="asap-1"><?php echo __('Get support as soon as possible', 'wp-testing') ?></h1>

<p><label for="Asap">
    <input name="asap" id="Asap" type="checkbox" value="1" <?php if ($asap): ?>checked="checked"<?php endif ?> /><?php
    echo __('Paid support', 'wp-testing')
 ?></label></p>

<p class="description asap-1"><?php
    echo sprintf(__('Use this text to order <a target="_blank" href="%s">paid support</a>', 'wp-testing'),
        'https://docs.google.com/document/d/1eHQB69neQJ68xl3vT-x4cHERZTBskq2L0x47AjUPyKM/edit?usp=sharing')
?></p>

<div class="asap-1 text-to-html">
<?php echo $text?>
</div>

<p class="description asap-0"><?php echo sprintf(__('Copy this text to create new topic at %s', 'wp-testing'),
    '<a href="https://wordpress.org/support/plugin/wp-testing/#new-post">wordpress.org/support/plugin/wp-testing</a>')
?></p>

<p class="asap-0"><textarea rows="20" cols="50" class="large-text code" onfocus="this.select()">
<?php echo $text?>
</textarea></p>

<p class="submit asap-0">
    <a href="https://wordpress.org/support/plugin/wp-testing/#new-post" class="button button-primary"><?php echo __('Next', 'wp-testing') ?></a>
</p>

</div>