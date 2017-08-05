<?php
/* @var $parameters array */
?>
<?php if (!empty($parameters)): ?>
<strong>Technical details</strong>

<?php foreach ($parameters as $value): ?>
<?php echo $value ?>


<?php endforeach ?>
<?php endif ?>