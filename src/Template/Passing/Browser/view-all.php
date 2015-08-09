<?php
/* @var $page integer */
/* @var $title string */
/* @var $table WpTesting_Widget_PassingTable */
/* @var $isSemanticHeaders boolean */
?>
<div class="wrap">

<?php if ($isSemanticHeaders): ?>
    <h1><?php echo $title ?></h1>
<?php else: ?>
    <h2><?php echo $title ?></h2>
<?php endif ?>

    <?php $table->views() ?>

    <form id="passings-filter" class="<?php echo $table->get_form_classes() ?>" method="get">
        <input type="hidden" name="page" value="<?php echo $page ?>" />
        <?php $table->display() ?>
    </form>

</div>