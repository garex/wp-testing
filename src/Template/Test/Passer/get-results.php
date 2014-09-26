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

    <ul>
    <?php foreach ($scales as $scale): /* @var $scale WpTesting_Model_Scale */ ?>

        <li><?php echo $scale->getTitle() ?>: <?php echo $scale->getScoresTotal() ?></li>

    <?php endforeach ?>
    </ul>

</div>

<hr/>

<div class="content">
    <?php echo $content ?>
</div>

</div>
