<?php
// Can be overriden in your theme as entry-content-wpt-test-get-results.php

/* @var $content string */
/* @var $test WpTesting_Model_Test */
/* @var $passing WpTesting_Model_Passing[] */
/* @var $scales WpTesting_Model_Scale[] */
?>
<div class="wpt_test get_results">

<div class="results">

    <h2><?php echo 'Results' ?></h2>

    <?php foreach ($scales as $scale): /* @var $scale WpTesting_Model_Scale */ ?>

        <h3 class="scale title"><?php echo $scale->getTitle() ?></h3>

        <div class="scale scores">
            <?php echo $scale->getScoresTotal() ?> <?php echo 'out of' ?> <?php echo $scale->getTotalScale()->getScoresTotal() ?>
        </div>
        <div class="meter">
            <span style="width: <?php echo $scale->getScoresTotalPercent() ?>%"></span>
        </div>

        <p class="scale description"><?php echo nl2br($scale->getDescription()) ?></p>

    <?php endforeach ?>

</div>

<hr/>

<div class="content">
    <?php echo $content ?>
</div>

</div>
