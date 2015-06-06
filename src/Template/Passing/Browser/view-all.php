<?php
/* @var $page integer */
/* @var $title string */
/* @var $table WpTesting_Widget_PassingTable */
?>
<div class="wrap">

    <h2><?php echo $title ?></h2>

    <?php $table->views() ?>

    <form id="passings-filter" class="<?php echo $table->get_form_classes() ?>" method="get">
        <input type="hidden" name="page" value="<?php echo $page ?>" />
        <?php $table->display() ?>
    </form>

</div>