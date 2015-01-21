<?php
// Can be overriden in your theme as entry-content-wpt-test-get-results.php

/* @var $content string */
/* @var $test WpTesting_Model_Test */
/* @var $passing WpTesting_Model_Passing[] */
/* @var $scales WpTesting_Model_Scale[] */
/* @var $results WpTesting_Model_Result[] */
/* @var $isShowScales boolean */
/* @var $isShowDescription boolean */
?>
<div class="wpt_test get_results">

<div class="results">

    <h2><?php echo __('Results', 'wp-testing') ?></h2>

    <?php foreach ($results as $result): /* @var $result WpTesting_Model_Result */ ?>

        <h3 class="result title"><?php echo $result->getTitle() ?></h4>

        <p class="result description"><?php echo nl2br($result->getDescription()) ?></p>

    <?php endforeach ?>

<?php if ($isShowScales): ?>

    <?php if (count($results)): ?>
        <hr/>
    <?php endif ?>

    <?php foreach ($scales as $scale): /* @var $scale WpTesting_Model_Scale */ ?>

        <h3 class="scale title"><?php echo $scale->getTitle() ?></h4>

        <div class="scale scores">
            <?php echo __(sprintf(__('%1$d out of %2$d', 'wp-testing'), $scale->getValue(), $scale->getMaximum()), 'wp-testing') ?>
        </div>
        <div class="meter">
            <span style="width: <?php echo $scale->getValueAsRatio()*100 ?>%"></span>
        </div>

        <p class="scale description"><?php echo nl2br($scale->getDescription()) ?></p>

    <?php endforeach ?>

<?php endif ?>

</div>

<?php if ($isShowDescription): ?>

<hr/>

<div class="content">
    <?php echo $content ?>
</div>

<?php endif ?>

</div>
