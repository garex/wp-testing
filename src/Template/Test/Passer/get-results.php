<?php
// Can be overriden in your theme as entry-content-wpt-test-get-results.php

/* @var $content string */
/* @var $renderer WpTesting_Doer_IRenderer */
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

    <?php foreach ($results as $i => $result): /* @var $result WpTesting_Model_Result */ ?>

        <h3 class="<?php echo $result->getCssClass($i) ?> title"><?php echo $result->getTitle() ?></h4>

        <div class="<?php echo $result->getCssClass($i) ?> description"><?php echo $renderer->renderWithMoreSplitted($renderer->renderTextAsHtml($result->getDescription())) ?></div>

    <?php endforeach ?>

<?php if ($isShowScales && count($results)): ?>
    <hr/>
<?php endif ?>

<?php if ($isShowScalesDiagram): ?>
    <div class="scales diagram"></div>
<?php endif ?>

<?php if ($isShowScales): ?>

    <?php foreach ($scales as $i => $scale): /* @var $scale WpTesting_Model_Scale */ ?>

        <h3 class="<?php echo $scale->getCssClass($i) ?> title"><?php echo $scale->getTitle() ?></h4>

        <div class="<?php echo $scale->getCssClass($i) ?> scores">
            <?php echo sprintf(__('%1$d out of %2$d', 'wp-testing'), $scale->getValue(), $scale->getMaximum()) ?>
        </div>
        <div class="<?php echo $scale->getCssClass($i) ?> meter">
            <span style="width: <?php echo $scale->getValueAsRatio()*100 ?>%"></span>
        </div>

        <div class="<?php echo $scale->getCssClass($i) ?> description"><?php echo $renderer->renderWithMoreSplitted($renderer->renderTextAsHtml($scale->getDescription())) ?></div>

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
