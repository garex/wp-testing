<?php
// Can be overriden in your theme as entry-content-wpt-test-get-results.php

/* @var $content string */
/* @var $test WpTesting_Model_Test */
/* @var $passing WpTesting_Model_Passing[] */
/* @var $scales WpTesting_Model_Scale[] */
/* @var $results WpTesting_Model_Result[] */
?>
<div class="wpt_test get_results">

<div class="results">

    <h2><?php echo 'Results' ?></h2>

    <?php foreach ($results as $result): /* @var $result WpTesting_Model_Result */ ?>

        <h4 class="result title"><?php echo $result->getTitle() ?></h4>

        <p class="result description"><?php echo nl2br($result->getDescription()) ?></p>

    <?php endforeach ?>

    <?php if (!empty($results)): ?>
        <hr/>
    <?php endif ?>

    <?php foreach ($scales as $scale): /* @var $scale WpTesting_Model_Scale */ ?>

        <h4 class="scale title"><?php echo $scale->getTitle() ?></h4>

        <div class="scale scores">
            <?php echo $scale->getValue() ?> <?php echo 'out of' ?> <?php echo $scale->getMaximum() ?>
        </div>
        <div class="meter">
            <span style="width: <?php echo $scale->getValueAsRatio()*100 ?>%"></span>
        </div>

        <p class="scale description"><?php echo nl2br($scale->getDescription()) ?></p>

    <?php endforeach ?>

</div>

<hr/>

<div class="content">
    <?php echo $content ?>
</div>

</div>
