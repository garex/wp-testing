<?php
/* @var $issueRepeats array */
/* @var $expected string */
/* @var $actual string */
/* @var $screenshot string */
/* @var $steps string */
/* @var $parameters string */
?>
<div class="wrap">
<h1><?php echo __('Report the problem', 'wp-testing')?></h1>

<p class="description"><?php echo sprintf(__('Copy this text to create new topic at %s', 'wp-testing'),
    '<a href="https://wordpress.org/support/plugin/wp-testing/#new-post">wordpress.org/support/plugin/wp-testing</a>')
?></p>

<p><textarea rows="20" cols="50" class="large-text code" onfocus="this.select()">
<?php if (!empty($issueRepeats)): ?>
<strong>Issue repeats</strong>
<ul>
<?php foreach ($issueRepeats as $value): ?>
    <li><?php echo $value ?></li>
<?php endforeach ?>
</ul>


<?php endif ?>
<?php if (!empty($expected)): ?>
<strong>Expected</strong>
<?php echo $expected ?>


<?php endif ?>
<?php if (!empty($actual)): ?>
<strong>Actual</strong>
<?php echo $actual ?>


<?php endif ?>
<?php if (!empty($screenshot)): ?>
<strong>Screenshot</strong>
<?php if (preg_match('/\.(png|jpe?g|gif|bmp)$/', $screenshot)): ?>
<img src="<?php echo $screenshot ?>" alt="Screenshot" />
<?php else: ?>
<?php echo $screenshot ?>
<?php endif ?>


<?php endif ?>
<?php if (!empty($steps)): ?>
<strong>Stept to repeat</strong>
<?php echo $steps?>


<?php endif ?>
<?php echo $parameters ?>
</textarea></p>

<p class="submit">
    <a href="https://wordpress.org/support/plugin/wp-testing/#new-post" class="button button-primary"><?php echo __('Next', 'wp-testing') ?></a>
</p>

</div>