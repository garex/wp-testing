<?php
/* @var $title string */
/* @var $details string */
/* @var $parameters string */
?>
<?php if (!empty($title)): ?>
<strong>In short</strong>
<?php echo $title ?>


<?php endif ?>
<?php if (!empty($details)): ?>
<strong>Details</strong>
<?php echo $details ?>


<?php endif ?>
<?php echo $parameters ?>
